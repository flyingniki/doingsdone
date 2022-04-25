<?php

/** Получает данные из POST и приводит их к нужному типу
@param array $post - данные из массива $_POST
@return array - возвращаемый массив
*/
function getDataFromTaskForm($post) {
    $result = [];
    $result['name'] = $post['name'] ?? NULL;
    $result['project_id'] = (int)$post['project_id'] ?? NULL;
    $result['date'] = $post['date'] ?? NULL;
    return $result;
}

/** Безопасная обработка полученных данных
@param array $formData - данные из формы
@return array - результат обработки
*/
function formDataHandler(array $formData) {
    foreach ($formData as $key => $value) {
        $formData[$key] = trim(htmlspecialchars(stripslashes($formData[$key])));
    }
    return $formData;
}

/**
 * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
 *
 * Примеры использования:
 * is_date_valid('2019-01-01'); // true
 * is_date_valid('2016-02-29'); // true
 * is_date_valid('2019-04-31'); // false
 * is_date_valid('10.10.2010'); // false
 * is_date_valid('10/10/2010'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
 */
function isDateValid(string $date) : bool {
    $format_to_check = 'Y-m-d';
    $dateTimeObj = date_create_from_format($format_to_check, $date);

    return $dateTimeObj !== false && array_sum(date_get_last_errors()) === 0;
}

/** Проверяет дату завершения задачи
@param string $date - дата
@return string|null - результат проверки
*/
function isDateCorrect($date) {
    if ((strtotime($date) >= time())) {
        return true;
    }
    return false;
}

/** Проверяет дату по формату и условию
@param string $date
@return string|null - результат проверки
 */
function validateDate($date) {
    if (!isDateValid($date)) :
        return 'Введите дату в формате «ГГГГ-ММ-ДД»';
    elseif (!isDateCorrect($date)) :
        return 'Эта дата должна быть больше или равна текущей';
    else :
        return NULL;
    endif;
}

/** Проверка существования проекта
@param int $postProjectId - id выбранного проекта
@param array $projects - массив со списком проектов
@return string|null - результат проверки
 */
function validateProject($postProjectId, $projects) {
    $res = NULL;
    foreach ($projects as $project) {
        if ($postProjectId !== $project['id']) {
            $res = 'Error';
            break;
        }
    }
    return $res;
}

/** Проверяет, что обязательное поле "название задачи" заполнено
@param string $taskName
@return string|null - результат проверки
 */
function validateTaskName($taskName) {
    if (mb_strlen(trim($taskName)) == 0) {
        return 'Название задачи должно быть заполнено';
    }
    return NULL;
}

/** Проверка загруженного файла по формату и размеру
@param array $file - загруженный файл
@return string|null - результат проверки
*/
function validateFile($file) {
    if (isset($file)) {
        $file_name = $file['tmp_name'];
        $file_size = $file['size'];

        $file_type = !empty($file_name) ? mime_content_type($file_name) : NULL;

        if (isset($file_type) && $file_type !== 'application/pdf') {
            return 'Загрузите файл в формате PDF';
        }
        elseif ($file_size > 5000000) {
            return 'Максимальный размер файла: 5Мб';
        }
    }
    return NULL;
}

/** Проверка формы на ошибки
@param array $postData - данные из формы
@param array $file - прикрепленный файл
@param array $projects - список проектов
@return array - массив с ошибками
*/
function validateTaskForm($postData, $file, $projects) {
    $result = [];
    $result['name'] = $postData['name'] ?? NULL;
    $result['project_id'] = (int)$postData['project_id'];
    $result['date'] = $postData['date'] ?? NULL;
    echo 'Данные из формы: ';
    print_r($postData);
    $errors = [];
    $rules = [
        'name' => validateTaskName($result['name']),
        'project_id' => validateProject($result['project_id'], $projects),
        'date' => validateDate($result['date']),
        'file' => validateFile($file)
    ];
    echo 'Правила проверки: ';
    print_r($rules);
    foreach ($postData as $key => $value) {
        if (isset($rules[$key])) {
            $errors[$key] = $rules[$key];
        }
    }
    echo 'Ошибки: ';
    print_r($errors);
    return $errors;
}

/** Сохраняет задачу в БД
@param mysqli $conn - ресурс соединения с БД
@param array $data - данные из формы
@param array $file - загруженный файл
@param int $userId - id пользователя
@return mysqli_result|false - результат запроса
*/
function setTask($conn, $data, $file, $userId = 1) {
    $sql = "INSERT INTO tasks (`title`, `file`, `date_final`, `user_id`, `project_id`) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
        if ($stmt === false) {
            $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($conn);
            die($errorMsg);
        }
        if (mysqli_errno($conn) > 0) {
            $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($conn);
            die($errorMsg);
        }
    mysqli_stmt_bind_param($stmt, 'sssii', $data['name'], $file['name'], $data['date'], $userId, $data['project_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return $result;
}

/** Перемещает загрженный файл из временной директории в папку /uploads/
@param array $file - загруженный файл
@return bool - результат загрузки
 */
function fileUpload($file) {
    if (isset($file)) {
        $file_name = $file['name'];
        $file_path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
        return move_uploaded_file($file['tmp_name'], $file_path . $file_name);
    }
    return 'Ошибка загрузки';
}
