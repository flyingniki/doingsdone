<?php

/** Считает количество задач в проекте
@param array $project - данные об одном проекте (из цикла по массиву проектов)
@param array $tasks - список задач
@return int - итоговое количество задач
*/
function counter($project, $tasks) {
    $cnt = 0;
        foreach ($tasks as $task) {
            if($project['id'] === $task['project_id']):
                $cnt++;
            endif;
        }
    return $cnt;
}

/** Считает остаток времени в часах
@param string $taskDate - срок завершения задачи
@return int - разница в часах
*/
function hourRemain($taskDate) {
    $taskDate = strtotime($taskDate);
    $nowDate = time();
    $diff = ($taskDate - $nowDate);
    $diffHour = floor($diff / 3600);
    if ($diffHour <= 24):
        return true;
    elseif ($diffHour > 24):
        return false;
    endif;
    return $diffHour;
}
