<?php

require_once('init.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //$formData = getDataFromTaskForm($_POST);
    //$formData = formDataHandler($formData);
    $file = $_FILES['file'];
    //echo 'Данные из формы: ';
    //print_r($formData);
    $errors = validateTaskForm($_POST, $file, $projects);
    if(empty($errors)) {
        fileUpload($file);
        setTask($conn, $_POST, $file);
        //сохранить ссылку на файл главной странице
        //header("Location: /index.php");
    }
}

$content = includeTemplate('add-task.php', [
    'showCompleteTasks' => $showCompleteTasks,
    'projects' => $projects,
    'formData' => $_POST ?? NULL,
    'file' => $file ?? NULL
]);

$layout = includeTemplate('layout.php', [
    'content' => $content,
    'title' => 'Добавить задачу'
]);

print($layout);




