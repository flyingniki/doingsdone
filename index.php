<?php

require_once('init.php');

if ($userId !== NULL) {

    $projects = getProjects($conn, $userId); // список проектов
    $userName = $_SESSION['user']['userName'];
    $projectId = isset($_GET['project_id']) ? filterString($_GET['project_id']) : NULL;
    $searchString = isset($_GET['search']) ? filterString($_GET['search']) : NULL;

    if (isset($projectId)) {

        if (checkExist($conn, $projectId, $userId)) {
            $tasks = getTasks($conn, $userId, $projectId, $searchString);
        }
        else {
            exit ('Error 404');
        }
    }

    else {
        $tasks = getTasks($conn, $userId, NULL, $searchString);
    }

    $taskId = filter_input(INPUT_GET, 'task_id', FILTER_VALIDATE_INT);
    //echo '$taskId = '. $taskId. ' ';
    if (isset($taskId)) {
        foreach ($tasks as $task) {
            if ($taskId === $task['id']) {
                $taskStatus = $task['status'];
                //echo '$taskStatus = '. $taskStatus. ' ';
                invertTaskStatus($conn, $taskId, $taskStatus);
                header("Location: /index.php");
                break;
            }
        }
    }

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
