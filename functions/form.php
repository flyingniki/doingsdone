<?php

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
    if (!empty($file['name'])) {
        $file_tmp_name = $file['tmp_name'];
        $file_size = $file['size'];

        $file_type = mime_content_type($file_tmp_name);

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
function validateTaskForm($file, $projects) {
    $result = [];
    $result['name'] = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
    $result['project_id'] = filter_input(INPUT_POST, 'project_id', FILTER_VALIDATE_INT);
    $result['date'] = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_SPECIAL_CHARS);
    $result['file'] = $file ?? NULL;
    //echo 'Данные из формы: ';
    //print_r($result);
    $errors = [
        'name' => validateTaskName($result['name']),
        'project_id' => validateProject($result['project_id'], $projects),
        'date' => !empty($result['date']) ? validateDate($result['date']) : NULL,
        'file' => validateFile($result['file'])
    ];
    $errors = array_filter($errors);
    //echo 'Ошибки: ';
    //print_r($errors);
    return $errors;
}

/** Перемещает загруженный файл из временной директории в папку /uploads/
@param array $file - загруженный файл
@return bool - результат загрузки
 */
function fileUpload($file) {
    if (isset($file)) {
        $file_name = $file['name'];
        $file_path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
        return move_uploaded_file($file['tmp_name'], $file_path . $file_name);
    }
}

/** Проверяет заполненность обязательных полей
@param string $field - поле
@return string|null -  результат проверки
 */
function validateRequiredField($field) {
    if (mb_strlen(trim($field)) == 0) {
        return 'Это поле должно быть заполнено';
    }
    return NULL;
}

/** Валидация введенного email
@param string $email - введеный email
@param array $users - список пользователей
@return string|null - результат валидации
 */
function validateRegEmail($email, $users) {
    if (!validateRequiredField($email)) {
        if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
            foreach ($users as $user) {
                if ($email === $user['email']) {
                    return 'Пользователь с таким e-mail уже существует';
                    break;
                }
                return NULL;
            }
        }
        else {
            return 'E-mail введён некорректно';
        }
    }
    return validateRequiredField($email);
}

/** Проверка формы регистрации на ошибки
@param array $users - список пользователей
@return (array|null) - результат валидации
 */
function validateRegisterForm($users) {
    $result = [];
    $result['email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS);
    $result['password'] = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
    $result['name'] = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);

    $errors = [
        'email' => validateRegEmail($result['email'], $users),
        'password' => validateRequiredField($result['password']),
        'name' => validateRequiredField($result['name'])
    ];

    $errors = array_filter($errors);
    //echo 'Ошибки: ';
    //print_r($errors);
    return $errors;
}

/** Валидация введенного email в форме аутентификации
@param string $email - введеный email
@return string|null - результат валидации
 */
function validateAuthEmail($email) {
    if (!validateRequiredField($email)) {
        if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return NULL;
        }
        else {
            return 'E-mail введён некорректно';
        }
    }
    return validateRequiredField($email);
}

/** Проверка формы аутентификации на ошибки
@return (array|null) - результат валидации
 */
function validateAuthForm() {
    $result = [];
    $result['email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS);
    $result['password'] = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

    $errors = [
        'email' => validateAuthEmail($result['email']),
        'password' => validateRequiredField($result['password'])
    ];

    $errors = array_filter($errors);
    //echo 'Ошибки: ';
    //print_r($errors);
    return $errors;
}


