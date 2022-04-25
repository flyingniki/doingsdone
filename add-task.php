<?php

require_once('init.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = $_POST;
    $file = $_FILES['file'];
    $errors = validateTaskForm($data, $file, $projects);
    if(empty($errors)) {
        setTask($conn, $data, $file);
        isset($file) ? fileUpload($file) : NULL;
        header("Location: /index.php");
    }
    foreach ($errors as $key => $value) {
        if(isset($errors[$key])) {
            $classError[$key] = 'form__input--error';
        }
        else {
            $classError[$key] = '';
        }
    }
}

$content = includeTemplate('add-task.php', [
    'showCompleteTasks' => $showCompleteTasks,
    'projects' => $projects,
    'file' => $file ?? NULL,
    'class' => $classError ?? NULL,
    'errors' => $errors ?? NULL
]);

$layout = includeTemplate('layout.php', [
    'content' => $content,
    'title' => 'Добавить задачу'
]);

print($layout);




