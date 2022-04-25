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
    $res = 'Выбранный проект не существует';
    foreach ($projects as $project) {
        if ($postProjectId === $project['id']) {
            $res = NULL;
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
@param array $data - данные из формы
@param array $file - прикрепленный файл
@param array $projects - список проектов
@return array - массив с ошибками
*/
function validateTaskForm($data, $file, $projects) {
    $result = [];
    $result['name'] = $data['name'] ?? NULL;
    $result['project_id'] = (int)$data['project_id'] ?? NULL;
    $result['date'] = $data['date'] ?? NULL;
    $result['file'] = $file ?? NULL;
    //echo 'Данные из формы: ';
    //print_r($data);
    $errors = [];
    $rules = [
        'name' => validateTaskName($result['name']),
        'project_id' => validateProject($result['project_id'], $projects),
        'date' => !empty($result['date']) ? validateDate($result['date']) : NULL,
        'file' => validateFile($result['file'])
    ];
    //echo 'Правила проверки: ';
    //print_r($rules);
    foreach ($data as $key => $value) {
        if (isset($rules[$key])) {
            $errors[$key] = $rules[$key];
        }
    }
    if (isset($rules['file'])) {
        $errors['file'] = $rules['file'];
    }
    //echo 'Ошибки: ';
    //print_r($errors);
    return $errors;
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
