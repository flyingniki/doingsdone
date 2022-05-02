<?php

require_once('init.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post = filterArray($_POST);
    $errors = validateAuthForm();
    foreach ($errors as $key => $value) {
        $classError[$key] = 'form__input--error';
    }
    if(empty($errors)) {

    }
}

$content = includeTemplate('auth.php', [
    'post' => $post ?? [],
    'class' => $classError ?? [],
    'errors' => $errors ?? []
]);

$layout = includeTemplate('layout.php', [
    'content' => $content,
    'title' => 'Авторизация'
]);

print($layout);
