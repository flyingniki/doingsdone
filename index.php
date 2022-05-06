<?php

require_once('init.php');

if ($userId !== NULL) {

    $projects = getProjects($conn, $userId); // список проектов
    $userName = $_SESSION['user']['userName'];
    $projectId = isset($_GET['project_id']) ? filterString($_GET['project_id']) : NULL;
    $searchString = isset($_GET['search']) ? filterString($_GET['search']) : NULL;

    if (isset($projectId)) :
        if (checkExist($conn, $projectId, $userId)) :
            $tasks = getTasks($conn, $userId, $projectId, $searchString);
        else:
            exit ('Error 404');
        endif;
    else:
        $tasks = getTasks($conn, $userId, NULL, $searchString);
    endif;

    $content = includeTemplate('main.php', [
        'showCompleteTasks' => $showCompleteTasks,
        'projects' => $projects,
        'tasks' => $tasks,
        'searchString' => $searchString
    ]);
}
else {
    $content = includeTemplate('guest.php', []);
}

$layout = includeTemplate('layout.php', [
    'userName' => $userName ?? '',
    'content' => $content,
    'title' => 'Дела в порядке'
]);
//print_r($_SESSION);
//var_dump(getUserIdFromSession());
print($layout);
