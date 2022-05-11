<?php

require_once('init.php');

if ($userId !== NULL) {

    $projects = getProjects($conn, $userId); // список проектов
    $userName = $_SESSION['user']['userName'];
    $projectId = isset($_GET['project_id']) ? filterString($_GET['project_id']) : NULL;
    $searchString = isset($_GET['search']) ? filterString($_GET['search']) : NULL;
    $dateFilter = isset($_GET['filter']) ? filterString($_GET['filter']) : NULL;

    if (isset($projectId)) {

        if (checkExist($conn, $projectId, $userId)) {
            $tasks = getTasks($conn, $userId, $projectId, $searchString, $dateFilter);
        }
        else {
            exit ('Error 404');
        }
    }

    else {
        $tasks = getTasks($conn, $userId, NULL, $searchString, $dateFilter);
    }

    $taskId = filter_input(INPUT_GET, 'task_id', FILTER_VALIDATE_INT);
    $showCompleted = filter_input(INPUT_GET, 'show_completed', FILTER_VALIDATE_INT);

    if (isset($taskId)) {
        foreach ($tasks as $task) {
            if ($taskId === $task['id']) {
                $taskStatus = $task['status'];
                invertTaskStatus($conn, $taskId, $taskStatus);
                header("Location: /index.php");
                exit();
                break;
            }
        }
    }

    if(isset($showCompleted)) {
        $showCompleteTasks = $showCompleted;
    }
    else {
        $showCompleteTasks = 1;
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

print($layout);
