<?php

require_once('init.php');

if ($userId !== null) {

    $projects = getProjects($conn, $userId); // список проектов
    $userName = $_SESSION['user']['userName'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $post = filterArray($_POST);
        $file = $_FILES['file'];
        $errors = validateTaskForm($file, $projects);
        foreach ($errors as $key => $value) {
            $classError[$key] = 'form__input--error';
        }
        if(empty($errors)) {
            addTask($conn, $post, $file, $userId);
            fileUpload($file);
            header("Location: /index.php");
            exit();
        }
    }

    $content = includeTemplate('add-task.php', [
        'projects' => $projects,
        'post' => $post ?? [],
        'file' => $file ?? [],
        'class' => $classError ?? [],
        'errors' => $errors ?? []
    ]);

}
else {
    $content = includeTemplate('guest.php', []);
}

$layout = includeTemplate('layout.php', [
    'userName' => $userName ?? null,
    'content' => $content,
    'title' => 'Добавить задачу'
]);

print($layout);







