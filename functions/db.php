<?php

/** Устанавливает соединение с БД
@param array $config массив с параметрами конфигурации БД
@return mysqli ресурс соединения с БД
*/
function dbConnect($config) {
    $conn = mysqli_connect($config['host'], $config['user'], $config['password'], $config['database']);
    mysqli_options($conn, MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
    if ($conn === false) {
        print("Ошибка подключения: " . mysqli_connect_error());
    }
    else {
        mysqli_set_charset($conn, "utf8");
    }
    return $conn;
}

/** Устанавливает запрос к БД и выводит результат в виде ассоциативного массива
@param mysqli $conn ресурс соединения с БД
@param string $sql SQL-запрос в виде строки
@return array ответ запроса в виде двумерного массива
*/
function dbQuery($conn, $sql) {
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        $error = mysqli_error($conn);
	    print("Ошибка MySQL: " . $error);
    };
    $res_assoc = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $res_assoc;
}

/** Процесс формирования запроса для получения списка проектов
@param mysqli $conn ресурс соединения с БД
@param int $userId ID пользователя
@return array ответ запроса в виде двумерного массива
*/
function getProjects(mysqli $conn, int $userId) {
    $sql = "SELECT p.id, p.title, COUNT(t.id) AS tasks_count FROM projects p LEFT JOIN tasks t ON t.project_id = p.id WHERE p.user_id = {$userId} GROUP BY p.id";
    return dbQuery($conn, $sql);
}

/** Процесс формирования запроса для получения списка задач для всех проектов либо выбранного проекта
 *  и текущего пользователя с учетом строки поиска и фильтров по дате
@param mysqli $conn ресурс соединения с БД
@param int $userId ID пользователя
@param null|int $project_id целое число (идентификатор проекта)
@param null|string $searchString строка поиска
@return array ответ запроса в виде двумерного массива
*/
function getTasks(mysqli $conn, int $userId, ?int $project_id = NULL, ?string $searchString = NULL, ?string $dateFilter = NULL): array {
    $sql = "SELECT * FROM tasks t WHERE t.user_id = {$userId}";
    if ($project_id !== NULL) {
        $sql .= " AND t.project_id = {$project_id}";
    }
    if ($searchString !== NULL && $searchString !== '') {
        $sql .= " AND MATCH (t.title) AGAINST ('{$searchString}')";
    }
    if ($dateFilter === 'today') {
        $sql .= " AND t.date_final = CURDATE()";
    }
    if ($dateFilter === 'tomorrow') {
        $sql .= " AND t.date_final = DATE_ADD(CURDATE(), INTERVAL 1 DAY)";
    }
    if ($dateFilter === 'overdue') {
        $sql .= " AND t.date_final < CURDATE() AND t.status = 0";
    }
    $sql .= " ORDER BY date_add DESC";
    return dbQuery($conn, $sql);
}

/** Проверка существования заданного проекта для текущего пользователя
@param mysqli $conn ресурс соединения с БД
@param int $project_id целое число (идентификатор проекта)
@param int $userId идентификатор пользователя
@return bool результат выполнения запроса
*/
function checkExist($conn, $project_id, $userId) {
    $sql = "SELECT t.date_add, t.status, t.title, t.file, t.date_final, t.user_id, t.project_id FROM tasks t WHERE EXISTS (SELECT * FROM projects p WHERE t.project_id = {$project_id} AND t.user_id = {$userId})";
    if (dbQuery($conn, $sql)) :
        return true;
    else :
        return false;
    endif;
}

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = []) {
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
        die($errorMsg);
    }

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            }
            else if (is_string($value)) {
                $type = 's';
            }
            else if (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);

        if (mysqli_errno($link) > 0) {
            $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
            die($errorMsg);
        }
    }

    return $stmt;
}

/** Сохраняет задачу в БД
@param mysqli $conn ресурс соединения с БД
@param array $data данные из формы
@param array $file загруженный файл
@param int $userId id пользователя
@return mysqli_result|false результат запроса
*/
function addTask($conn, $data, $file, $userId) {
    $dataArray = [
        date('Y-m-d H-i-s'),
        $data['name'],
        $file['name'],
        $userId,
        $data['project_id']
    ];

    if (!empty($data['date'])) {
        $dataArray[] = $data['date'];
        $sql = "INSERT INTO tasks (`date_add`, `title`, `file`, `user_id`, `project_id`, `date_final`) VALUES (?, ?, ?, ?, ?, ?)";
    }
    else {
        $sql = "INSERT INTO tasks (`date_add`, `title`, `file`, `user_id`, `project_id`) VALUES (?, ?, ?, ?, ?)";
    }

    $stmt = db_get_prepare_stmt($conn, $sql, $dataArray);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result;
}

/** Процесс формирования запроса для получения списка пользователей
@param mysqli $conn - ресурс соединения с БД
@return array - ответ запроса в виде двумерного массива
*/
function getUsers(mysqli $conn) {
    $sql = 'SELECT * FROM users u';
    return dbQuery($conn, $sql);
}

/** Сохраняет пользователя в БД
@param mysqli $conn ресурс соединения с БД
@param array $data данные из формы
@return mysqli_result|false результат запроса
*/
function addUsers($conn, $data) {
    $dataArray = [
        $data['email'],
        $data['password'],
        $data['name']
    ];

    $sql = 'INSERT INTO users (`email`, `password`, `name`) VALUES (?, ?, ?)';

    $stmt = db_get_prepare_stmt($conn, $sql, $dataArray);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result;
}

/** Провера существования проекта с заданным названием
@param mysqli $conn ресурс соединения с БД
@param string $projectName название проекта
@param int $userId ID пользователя
@return string|null результат проверки
 */
function checkExistProjectName($conn, $projectName, $userId) {
    $sql = "SELECT * FROM projects p WHERE p.title = '{$projectName}' AND p.user_id = {$userId}";
    if (dbQuery($conn, $sql)) :
        return 'Проект с таким именем уже существует';
    else :
        return NULL;
    endif;
}

/** Функция добавления проекта авторизованным пользователем
@param mysqli $conn ресурс соединения с БД
@param array $data данные из формы
@param mixed $userId ID пользователя
@return mysqli_result|false результат запроса
 */
function addProjects($conn, $data, $userId) {
    $dataArray = [
        $data['name'],
        $userId
    ];

    $sql = "INSERT INTO projects (`title`, `user_id`) VALUES (?, ?)";

    $stmt = db_get_prepare_stmt($conn, $sql, $dataArray);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result;
}

/** Запрос статуса задачи
@param mysqli $conn ресурс соединения с БД
@param int $taskId ID задачи
@return array ответ запроса в виде двумерного массива
 */
function getTaskStatus($conn, $taskId) {
    $sql = "SELECT t.status FROM tasks t WHERE t.id = {$taskId}";
    return dbQuery($conn, $sql);
}

/** Функция изменения статуса задачи
@param mysqli $conn ресурс соединения с БД
@param int $taskId ID задачи
@param int $taskStatus текущий статус задачи
@return mysqli_result|false результат запроса
 */
function invertTaskStatus($conn, $taskId, $taskStatus) {
    if ($taskStatus === 0) {
        $sql = "UPDATE tasks t SET t.status = 1 WHERE t.id = {$taskId}";
    }
    elseif ($taskStatus === 1) {
        $sql = "UPDATE tasks t SET t.status = 0 WHERE t.id = {$taskId}";
    }
    return mysqli_query($conn, $sql);
}
