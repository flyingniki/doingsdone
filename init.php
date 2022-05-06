<?php

session_start();

require_once('config.php'); // параметры БД
require_once('functions/db.php'); // работа с БД
require_once('functions/calculation.php'); // вычисления
require_once('functions/template.php'); // шаблонизаторы
require_once('functions/form.php'); // обработка форм

$showCompleteTasks = rand(0, 1); // случайное число
$conn = dbConnect($config['db']); // записываем соединение в переменную
$userId = getUserIdFromSession(); // получаем ID пользователя из сессии
