<?php

require_once('init.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post = filterArray($_POST);
    $errors = validateAuthForm();
    foreach ($errors as $key => $value) {
        $classError[$key] = 'form__input--error';
    }
    if(empty($errors)) {
        $errors = checkAuth($post, $users);
        foreach ($errors as $key => $value) {
            $classError[$key] = 'form__input--error';
        }
        if (empty($errors)) {
            /*
            аутентифицировать пользователя и записывать в сессию информацию о нём;
            перенаправлять на главную страницу
            */
        }
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
