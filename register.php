<?php

require_once('init.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post = filterArray($_POST);
    $errors = validateRegisterForm($users);
    foreach ($errors as $key => $value) {
        $classError[$key] = 'form__input--error';
    }
    if (empty($errors)) {
        addUsers($conn, $post);
        header("Location: /index.php");
        exit();
    }
}

$content = includeTemplate('register.php', [
    'post' => $post ?? [],
    'class' => $classError ?? [],
    'errors' => $errors ?? []
]);

$layout = includeTemplate('layout.php', [
    'content' => $content,
    'title' => 'Регистрация аккаунта'
]);

print($layout);
