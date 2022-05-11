<?php

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

require_once ('vendor/autoload.php');
require_once ('init.php');

$tasks = getUncompletedTasks($conn);

foreach ($tasks as $task) {
    $taskUserId[] = $task['user_id'];
    $taskUserName[] = $task['name'];
    $taskUserEmail[] = $task['email'];
    $taskTitle[] = $task['title'];
}
print_r($tasks);

// Конфигурация траспорта
$dsn = 'smtp://4234:32434@smtp.mailtrap.io:2525?encryption=tls&auth_mode=login';
$transport = Transport::fromDsn($dsn);
// Формирование сообщения
$message = new Email();
$message->to("flying_niki@mail.ru");
$message->from("keks@phpdemo.ru");
$message->subject("Уведомление от сервиса «Дела в порядке»");
$message->text("Уважаемый, %имя пользователя%. У вас запланирована задача %имя задачи% на %время задачи%");
// Отправка сообщения
$mailer = new Mailer($transport);

