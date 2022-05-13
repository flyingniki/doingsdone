<?php

require_once('init.php');

if ($userId !== null) {

    $projects = getProjects($conn, $userId); // список проектов
    $userName = $_SESSION['user']['userName'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $post = filterArray($_POST);
        $projectName = $post['name'] ?? null;
        $errors = validateProjectForm();
        $classError = isset($errors) ? 'form__input--error' : null;

        if (empty($errors)) {
            $errors = checkExistProjectName($conn, $projectName, $userId);
            $classError = isset($errors) ? 'form__input--error' : null;

            if (empty($errors)) {
                addProjects($conn, $post, $userId);
                header("Location: /index.php");
                exit();
            }
        }
    }

    $content = includeTemplate('add-project.php', [
        'projects' => $projects,
        'post' => $post ?? [],
        'class' => $classError ?? '',
        'errors' => $errors ?? ''
    ]);
} else {
    $content = includeTemplate('guest.php', []);
}

$layout = includeTemplate('layout.php', [
    'userName' => $userName ?? null,
    'content' => $content,
    'title' => 'Добавить задачу'
]);

print($layout);
