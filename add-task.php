<?php

require_once('init.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post = $_POST;
    $file = $_FILES['file'];
    $errors = validateTaskForm($post, $file, $projects);
    foreach ($errors as $key => $value) {
        $classError[$key] = 'form__input--error';
    }
    if(empty($errors)) {
        addTask($conn, $post, $file);
        fileUpload($file);
        //header("Location: /index.php");
    }
}

$content = includeTemplate('add-task.php', [
    'showCompleteTasks' => $showCompleteTasks,
    'projects' => $projects,
    'post' => $post ?? NULL,
    'file' => $file ?? NULL,
    'class' => $classError ?? NULL,
    'errors' => $errors ?? NULL
]);

$layout = includeTemplate('layout.php', [
    'content' => $content,
    'title' => 'Добавить задачу'
]);

print($layout);




