<?php

require_once('init.php');

if (isset($_GET['project_id'])) :
    if (checkExist($conn, $_GET['project_id'])) :
        $tasks = getTasks($conn, $_GET['project_id']);
    else:
        exit ('Error 404');
    endif;
else:
    $tasks = getTasks($conn);
endif;

$content = includeTemplate('main.php', [
    'showCompleteTasks' => $showCompleteTasks,
    'projects' => $projects,
    'tasks' => $tasks
]);

$layout = includeTemplate('layout.php', [
    'content' => $content,
    'title' => 'Дела в порядке'
]);

print($layout);
