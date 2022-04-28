<?php

require_once('init.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $post = filterArray($_POST);
    $file = $_FILES['file'];
    $errors = validateTaskForm($file, $projects);
    foreach ($errors as $key => $value) {
        $classError[$key] = 'form__input--error';
    }
    if(empty($errors)) {
        addTask($conn, $post, $file);
        fileUpload($file);
        header("Location: /index.php");
        exit();
    }
}

$content = includeTemplate('add-task.php', [
    'showCompleteTasks' => $showCompleteTasks,
    'projects' => $projects,
    'post' => $post ?? [],
    'file' => $file ?? [],
    'class' => $classError ?? [],
    'errors' => $errors ?? []
]);

$layout = includeTemplate('layout.php', [
    'content' => $content,
    'title' => 'Добавить задачу'
]);

print($layout);




