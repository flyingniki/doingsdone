<?php

use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

require_once('vendor/autoload.php');
require_once('init.php');

$uncompletedTasks = getUncompletedTasks($conn);

$usersId = [];
$usersEmail = [];
$usersName = [];

foreach ($uncompletedTasks as $uncompletedTask) {
    if (!in_array($uncompletedTask['user_id'], $usersId)) {
        $usersId[] = $uncompletedTask['user_id'];
        $usersEmail[$uncompletedTask['user_id']] = $uncompletedTask['email'];
        $usersName[$uncompletedTask['user_id']] = $uncompletedTask['name'];
        $messageText[$uncompletedTask['user_id']] = '«' . $uncompletedTask['title'] . '» на ' . date('d-m-Y', strtotime($uncompletedTask['date_final']));
    } else {
        $messageText[$uncompletedTask['user_id']] .= ', «' . $uncompletedTask['title'] . '» на ' . date('d-m-Y', strtotime($uncompletedTask['date_final']));
    }
}
/*
echo '<pre>';
print_r($usersEmail);
print_r($usersName);
print_r($messageText);
echo '</pre>';
*/
$dsn = 'smtp://flying_niki@mail.ru:password@smtp.mail.ru:465?encryption=tls&auth_mode=login';
$transport = Transport::fromDsn($dsn);

foreach ($usersId as $Id) {
    $message = new Email();
    $message->to($usersEmail[$Id]);
    $message->from('mail@giftube.academy');
    $message->subject('Уведомление от сервиса «Дела в порядке»');
    $message->text('Уважаемый, ' . $usersName[$Id] . '. У вас запланирована задача ' . $messageText[$Id]);
    $mailer = new Mailer($transport);
    /*
    echo '<pre>';
    print_r($message);
    echo '</pre>';
    */
}
