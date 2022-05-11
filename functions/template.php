<?php

/** Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
@param string $tmp_name Имя файла шаблона
@param array $data Ассоциативный массив с данными для шаблона
@return string Итоговый HTML
*/
function includeTemplate($tmpName, array $data = []) {
    $tmpName = 'templates/' . $tmpName;
    if (!is_readable($tmpName)) {
        return;
    };
    ob_start();
    extract($data);
    require $tmpName;
    $result = ob_get_clean();
    return $result;
}

/** Добавляет класс к активному пункту меню
@param array $project данные об одном проекте (из цикла по массиву проектов)
@return string возвращает название класса либо пустую строку
*/
function getClassForMenuItem($project) {
    return (isset($_GET['project_id']) && $_GET['project_id'] === $project['id']) ?
        'main-navigation__list-item--active' :
        '';
}

/** Формирует ссылку на проект
@param string $scenario имя страницы
@param array $params параметр запроса с идентификатором проекта
@return string адрес ссылки
*/
function buildUrlForProject($scenario, $params) {
    $path = '/' . $scenario . '?';
    $query = http_build_query($params);
    $url = $path . $query;
    return $url;
}

/** Формирует ссылку для фильтра
@param string $scenario имя страницы
@param array $params параметр запроса с названием фильтра
@return string адрес ссылки
*/
function buildUrlForFilter($scenario, $params) {
    $path = '/' . $scenario . '?';
    $query = http_build_query($params);
    $url = $path . $query;
    return $url;
}

