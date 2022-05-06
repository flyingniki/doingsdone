<?php

require_once('init.php');

if ($userId !== NULL) {

    $projects = getProjects($conn, $userId); // список проектов
    $userName = $_SESSION['user']['userName'];

    if (isset($_GET['project_id'])) :
        if (checkExist($conn, $_GET['project_id'])) :
            $tasks = getTasks($conn, $userId, $_GET['project_id']);
        else:
            exit ('Error 404');
        endif;
    else:
        $tasks = getTasks($conn, $userId);
    endif;

    $content = includeTemplate('main.php', [
        'showCompleteTasks' => $showCompleteTasks,
        'projects' => $projects,
        'tasks' => $tasks
    ]);
}
else {
    $content = includeTemplate('guest.php', []);
}

$layout = includeTemplate('layout.php', [
    'userName' => $userName ?? NULL,
    'content' => $content,
    'title' => 'Дела в порядке'
]);
//print_r($_SESSION);
//var_dump(getUserIdFromSession());
print($layout);
