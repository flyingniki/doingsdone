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
        'name' => $task['name'],
        'title' => $task['title'],
        'date_final' => $task['date_final']
    ];
}
/*
echo '<pre>';
print_r($usersTasks);
echo '</pre>';
*/
foreach ($usersTasks as $userEmail => $userTask) {
    $taskTitle = '';
    $taskDateFinal = '';
    foreach ($userTask as $taskInfo) {
        $taskUserName = $taskInfo['name'];
        $taskTitle .= '«' . $taskInfo['title']. '» ';
        $taskDateFinal = $taskInfo['date_final'];
    }

    // Конфигурация траспорта
    $dsn = 'smtp://flying_niki@mail.ru:password@smtp.mail.ru:465?encryption=tls&auth_mode=login';
    $transport = Transport::fromDsn($dsn);
    // Формирование сообщения
    $message = new Email();
    $message->to($userEmail);
    $message->from("flying_niki@mail.ru");
    $message->subject("Уведомление от сервиса «Дела в порядке»");
    $message->text("Уважаемый, ". $taskUserName . ". У вас запланирована задача " . $taskTitle. "на " . date('d-m-Y', strtotime($taskDateFinal)));
    // Отправка сообщения
    $mailer = new Mailer($transport);
    /*
    echo '<pre>';
    print_r($message);
    echo '</pre>';
    */
}
