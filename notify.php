<?php

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

require_once ('vendor/autoload.php');
require_once ('init.php');

$tasks = getUncompletedTasks($conn);
$usersTasks = [];

foreach ($tasks as $task) {
    $usersTasks[$task['email']][] = [
        'title' => $task['title'],
        'date_final' => $task['date_final']
    ];
}

echo '<pre>';
print_r($usersTasks);
echo '</pre>';

foreach ($usersTasks as $userTask) {
}
/*
// Конфигурация траспорта
$dsn = 'smtp://4234:32434@smtp.mailtrap.io:2525?encryption=tls&auth_mode=login';
$transport = Transport::fromDsn($dsn);
// Формирование сообщения
$message = new Email();
$message->to($taskUserEmail);
$message->from("keks@phpdemo.ru");
$message->subject("Уведомление от сервиса «Дела в порядке»");
$message->text("Уважаемый, ". $taskUserName . ".У вас запланирована задача " . $taskTitle. " на " . $taskDateFinal);
// Отправка сообщения
$mailer = new Mailer($transport);
*/
