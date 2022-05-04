<?php

require_once('init.php');

if (!empty($_SESSION['user']['userId'])) {
    header("Location: /index.php");
}

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
            foreach ($users as $user) {
                if ($post['email'] === $user['email']) {
                    $_SESSION['user']['email'] = $user['email'];
                    $_SESSION['user']['userId'] = $user['id'];
                    $_SESSION['user']['userName'] = $user['name'];
                    break;
                }
            }
            header("Location: /index.php");
            exit();
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
