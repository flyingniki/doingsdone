<?php

/** Устанавливает соединение с БД
@param array $config - массив с параметрами конфигурации БД
@return mysqli - ресурс соединения с БД
*/
function dbConnect($config) {
    $conn = mysqli_connect($config['host'], $config['user'], $config['password'], $config['database']);
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
    $sql = 'SELECT t.date_add, t.status, t.title, t.file, t.date_final, t.user_id, t.project_id FROM tasks t';
    if (isset($project_id)) {
        $sql .= " WHERE t.project_id = {$project_id}";
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
