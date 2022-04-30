<?php

/** Устанавливает соединение с БД
@param array $config - массив с параметрами конфигурации БД
@return mysqli - ресурс соединения с БД
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
@param mysqli $conn - ресурс соединения с БД
@param string $sql - SQL-запрос в виде строки
@return array - ответ запроса в виде двумерного массива
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
@param mysqli $conn - ресурс соединения с БД
@return array - ответ запроса в виде двумерного массива
*/
function getProjects(mysqli $conn) {
    $sql = 'SELECT p.id, p.title, COUNT(t.id) AS tasks_count FROM projects p LEFT JOIN tasks t ON t.project_id = p.id GROUP BY p.id';
    return dbQuery($conn, $sql);
}

/** Процесс формирования запроса для получения списка задач для всех либо каждого проекта
@param mysqli $conn - ресурс соединения с БД
@param int $project_id - целое число (идентификатор проекта)
@return array - ответ запроса в виде двумерного массива
*/
function getTasks(mysqli $conn, ?int $project_id = NULL): array {
    $sql = 'SELECT t.date_add, t.status, t.title, t.file, t.date_final, t.user_id, t.project_id FROM tasks t ORDER BY date_add DESC';
    if (isset($project_id)) {
        $sql = "SELECT t.date_add, t.status, t.title, t.file, t.date_final, t.user_id, t.project_id FROM tasks t WHERE t.project_id = {$project_id} ORDER BY date_add DESC";
    }
    return dbQuery($conn, $sql);
}

/** Проверка существования записи в таблице БД
@param mysqli $conn - ресурс соединения с БД
@param int $project_id - целое число (идентификатор проекта)
@return bool - результат выполнения запроса
*/
function checkExist($conn, $project_id) {
    $sql = "SELECT t.date_add, t.status, t.title, t.file, t.date_final, t.user_id, t.project_id FROM tasks t WHERE EXISTS (SELECT * FROM projects p WHERE p.id = {$project_id})";
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
@param mysqli $conn - ресурс соединения с БД
@param array $data - данные из формы
@param array $file - загруженный файл
@param int $userId - id пользователя
@return mysqli_result|false - результат запроса
*/
function addTask($conn, $data, $file, $userId = 1) {
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

/* Приводит данные в безопасное представление (удаляет пробелы и очищает от html-тегов)
@param string $data
@return string результат
*/
function filterString($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

/* Приводит данные в безопасное представление (удаляет пробелы и очищает от html-тегов)
@param array $data
@return array результат
*/
function filterArray($data) {
    $result = [];
    foreach ($data as $key => $value) {
        $result[$key] = filterString($value);
    }
    return $result;
}

/** Процесс формирования запроса для получения email у списка пользователей
@param mysqli $conn - ресурс соединения с БД
@return array - ответ запроса в виде двумерного массива
*/
function getUsers(mysqli $conn) {
    $sql = 'SELECT * FROM users u';
    return dbQuery($conn, $sql);
}

/** Сохраняет пользователя в БД
@param mysqli $conn - ресурс соединения с БД
@param array $data - данные из формы
@return mysqli_result|false - результат запроса
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

