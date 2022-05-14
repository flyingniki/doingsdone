<?php

/** Приводит данные в безопасное представление (удаляет пробелы и очищает от html-тегов)
@param string $data
@return string результат
*/
function filterString($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

/** Приводит данные в безопасное представление (удаляет пробелы и очищает от html-тегов)
@param array $data
@return array результат
*/
function filterArray($data)
{
    $result = [];
    foreach ($data as $key => $value) {
        $result[$key] = filterString($value);
    }
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
function isDateValid(string $date): bool
{
    $format_to_check = 'Y-m-d';
    $dateTimeObj = date_create_from_format($format_to_check, $date);

    return $dateTimeObj !== false && array_sum(date_get_last_errors()) === 0;
}

/** Проверяет дату завершения задачи
@param string $date дата
@return string|null результат проверки
 */
function isDateCorrect($date)
{
    if ((strtotime($date) >= time())) {
        return true;
    }
    return false;
}

/** Проверяет дату по формату и условию
@param string $date дата
@return string|null результат проверки
 */
function validateDate($date)
{
    if (!isDateValid($date)) :
        return 'Введите дату в формате «ГГГГ-ММ-ДД»';
    elseif (!isDateCorrect($date)) :
        return 'Эта дата должна быть больше или равна текущей';
    else :
        return null;
    endif;
}

/** Проверка существования проекта
@param int $postProjectId id выбранного проекта
@param array $projects массив со списком проектов
@return string|null результат проверки
 */
function validateProject($postProjectId, $projects)
{
    $res = 'Выбранный проект не существует';
    foreach ($projects as $project) {
        if ($postProjectId === $project['id']) {
            $res = null;
            break;
        }
    }
    return $res;
}

/** Проверяет, что обязательное поле "название задачи" заполнено
@param string $taskName название задачи
@return string|null результат проверки
 */
function validateTaskName($taskName)
{
    if (mb_strlen(trim($taskName)) == 0) {
        return 'Название задачи должно быть заполнено';
    }
    return null;
}

/** Проверка загруженного файла по формату и размеру
@param array $file загруженный файл
@return string|null результат проверки
 */
function validateFile($file)
{
    if (!empty($file['name'])) {
        $file_tmp_name = $file['tmp_name'];
        $file_size = $file['size'];

        $file_type = mime_content_type($file_tmp_name);

        if (isset($file_type) && $file_type !== 'application/pdf') {
            return 'Загрузите файл в формате PDF';
        } elseif ($file_size > 5000000) {
            return 'Максимальный размер файла: 5Мб';
        }
    }
    return null;
}

/** Получает и фильтрует данные из формы для последующей валидации
@param array $file прикрепленный файл
@return array результат фильтрации
 */
function getTaskFormData($file)
{
    $result = [];
    $result['name'] = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS) ?? null;
    $result['project_id'] = filter_input(INPUT_POST, 'project_id', FILTER_VALIDATE_INT) ?? null;
    $result['date'] = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_SPECIAL_CHARS) ?? null;
    $result['file'] = $file['name'] ?? null;
    return $result;
}

/** Валидация формы добавления задачи на ошибки
@param array $data данные из формы
@param array $file прикрепленный файл
@param array $projects список проектов
@return array массив с ошибками
 */
function validateTaskForm($file, $projects)
{
    $result = getTaskFormData($file);
    //echo 'Данные из формы: ';
    //print_r($result);
    $errors = [
        'name' => validateTaskName($result['name']),
        'project_id' => validateProject($result['project_id'], $projects),
        'date' => !empty($result['date']) ? validateDate($result['date']) : null,
        'file' => validateFile($result['file'])
    ];
    $errors = array_filter($errors);
    //echo 'Ошибки: ';
    //print_r($errors);
    return $errors;
}

/** Перемещает загруженный файл из временной директории в папку /uploads/
@param array $file загруженный файл
@return bool результат загрузки
 */
function fileUpload($file)
{
    if (isset($file)) {
        $file_name = $file['name'];
        $file_path = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
        return move_uploaded_file($file['tmp_name'], $file_path . $file_name);
    }
}

/** Проверяет заполненность обязательных полей
@param string $field поле ввода
@return string|null  результат проверки
 */
function validateRequiredField($field)
{
    if (mb_strlen(trim($field)) == 0) {
        return 'Это поле должно быть заполнено';
    }
    return null;
}

/** Проверка email в форме регистрации
@param string $email введеный email
@param array $users список пользователей
@return string|null результат валидации
 */
function validateRegEmail($email, $users)
{
    if (!validateRequiredField($email)) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            foreach ($users as $user) {
                if ($email === $user['email']) {
                    return 'Пользователь с таким e-mail уже существует';
                    break;
                }
                return null;
            }
        } else {
            return 'E-mail введён некорректно';
        }
    }
    return validateRequiredField($email);
}

/** Получает и фильтрует данные из формы для последующей валидации
@return array результат фильтрации
 */
function getRegisterFormData()
{
    $result = [];
    $result['email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS) ?? null;
    $result['password'] = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS) ?? null;
    $result['name'] = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS) ?? null;
    return $result;
}

/** Валидация формы регистрации на ошибки
@param array $users список пользователей
@return array|null результат валидации
 */
function validateRegisterForm($users)
{
    $result = getRegisterFormData();

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

/** Проверка email в форме аутентификации
@param string $email введеный email
@return string|null результат валидации
 */
function validateAuthEmail($email)
{
    if (!validateRequiredField($email)) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        } else {
            return 'E-mail введён некорректно';
        }
    }
    return validateRequiredField($email);
}

/** Получает и фильтрует данные из формы для последующей валидации
@return array результат фильтрации
 */
function getAuthFormData()
{
    $result = [];
    $result['email'] = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS) ?? null;
    $result['password'] = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS) ?? null;
    return $result;
}

/** Валидация формы аутентификации на ошибки
@return (array|null) результат валидации
 */
function validateAuthForm()
{
    $result = getAuthFormData();

    $errors = [
        'email' => validateAuthEmail($result['email']),
        'password' => validateRequiredField($result['password'])
    ];

    $errors = array_filter($errors);
    //echo 'Ошибки: ';
    //print_r($errors);
    return $errors;
}

/** Проверка авторизации
@param array $data данные из формы авторизации
@param array $users массив существующих пользователей
@return array результат проверки
*/
function checkAuth($data, $users)
{
    $login = $data['email'];
    $password = $data['password'];
    $errors = [];
    $errors['email'] = 'Неверное имя пользователя';
    foreach ($users as $user) {
        if ($login === $user['email']) {
            $errors['email'] = '';
            $passwordHash = password_hash($user['password'], PASSWORD_DEFAULT);
            if (password_verify($password, $passwordHash)) {
                $errors['password'] = null;
                break;
            } else {
                $errors['password'] = 'Неверный пароль';
            }
        }
    }
    $errors = array_filter($errors);
    return $errors;
}

/** Получение ID пользователя из сессии
@return int ID пользователя
*/
function getUserIdFromSession()
{
    $userId = $_SESSION['user']['userId'] ?? null;
    return $userId;
}

/** Получает и фильтрует данные из формы для последующей валидации
@return array результат фильтрации
 */
function getProjectFormData()
{
    $result = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS) ?? null;
    return $result;
}

/** Проверяет, что обязательное поле "название проекта" заполнено
@param string $projectName название задачи
@return string|null результат проверки
 */
function validateProjectName($projectName)
{
    if (mb_strlen(trim($projectName)) == 0) {
        return 'Название проекта должно быть заполнено';
    }
    return null;
}

/** Валидация формы добавления проекта на ошибки
 * @return string|null результат валидации
*/
function validateProjectForm()
{
    $result = getProjectFormData();
    $errors = validateProjectName($result);
    return $errors;
}
