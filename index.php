<?php

require_once('init.php');

if (getUserIdFromSession()) {

    if (isset($_GET['project_id'])) :
        if (checkExist($conn, $_GET['project_id'])) :
            $tasks = getTasks($conn, $_GET['project_id']);
        else:
            exit ('Error 404');
        endif;
    else:
        $tasks = getTasks($conn);
    endif;

    $userName = $_SESSION['user']['userName'];

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
