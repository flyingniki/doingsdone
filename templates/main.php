<section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>

    <nav class="main-navigation">
        <ul class="main-navigation__list">
            <?foreach ($projects as $project):?>
            <li class="main-navigation__list-item">
                <a class="main-navigation__list-item-link <?= getClassForMenuItem($project) ?>"
                    href=<?= buildUrlForProject('index.php', ['project_id' => $project['id']]) ?>><?= filterString($project['title']) ?></a>
                <span class="main-navigation__list-item-count"><?= $project['tasks_count'] ?></span>
            </li>
            <?endforeach;?>
        </ul>
    </nav>

    <a class="button button--transparent button--plus content__side-button" href="/add-project.php"
        target="project_add">Добавить проект</a>
</section>

<main class="content__main">
    <h2 class="content__main-heading">Список задач</h2>

    <form class="search-form" action="index.php" method="get" autocomplete="off">
        <input class="search-form__input" type="text" name="search" value="<?= $searchString ?>"
            placeholder="Поиск по задачам">

        <input class="search-form__submit" type="submit" name="" value="Искать">
    </form>

    <div class="tasks-controls">
        <nav class="tasks-switch">
            <a href="/" class="tasks-switch__item tasks-switch__item--active">Все задачи</a>
            <a href="/" class="tasks-switch__item">Повестка дня</a>
            <a href="/" class="tasks-switch__item">Завтра</a>
            <a href="/" class="tasks-switch__item">Просроченные</a>
        </nav>

        <label class="checkbox">
            <? if ($showCompleteTasks): ?>
            <input class="checkbox__input visually-hidden show_completed" type="checkbox" checked>
            <? else: ?>
            <input class="checkbox__input visually-hidden show_completed" type="checkbox">
            <? endif; ?>
            <span class="checkbox__text">Показывать выполненные</span>
        </label>
    </div>

    <table class="tasks">
        <? if (empty($tasks)) {
            echo 'Ничего не найдено по вашему запросу';
        }
            else {
                foreach ($tasks as $task) {

                    if (!$showCompleteTasks && $task['status']):
                        continue;
                    endif;

                    if (!$task['status']):
                        if (hourRemain($task['date_final']) && $task['date_final'] !== NULL):?>

                            <tr class="tasks__item task task--important">
                                <td class="task__select">
                                    <label class="checkbox task__checkbox">
                                        <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" value="1">
                                        <span class="checkbox__text"><?= filterString($task['title']) ?></span>
                                    </label>
                                </td>

                                <td class="task__file">
                                    <a class="<?= $task['file'] ? 'download-link' : '' ?>"
                                        href="/uploads/<?= $task['file'] ?>"><?= $task['file'] ?></a>
                                </td>

                                <td class="task__date"><?= $task['date_final'] ?></td>
                            </tr>

                        <? else: ?>

                            <tr class="tasks__item task">
                                <td class="task__select">
                                    <label class="checkbox task__checkbox">
                                        <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" value="1">
                                        <span class="checkbox__text"><?= filterString($task['title']) ?></span>
                                    </label>
                                </td>

                                <td class="task__file">
                                    <a class="<?= $task['file'] ? 'download-link' : '' ?>"
                                        href="/uploads/<?= $task['file'] ?>"><?= $task['file'] ?></a>
                                </td>

                                <td class="task__date"><?= $task['date_final'] ?></td>
                            </tr>

                        <? endif; ?>
        <!--показывать следующий тег <tr/>, если переменная $showCompleteTasks равна единице-->

                    <? else: ?>

                        <tr class="tasks__item task task--completed">
                            <td class="task__select">
                                <label class="checkbox task__checkbox">
                                    <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" value="1">
                                    <span class="checkbox__text"><?= filterString($task['title']) ?></span>
                                </label>
                            </td>

                            <td class="task__file">
                                <a class="<?= $task['file'] ? 'download-link' : '' ?>"
                                    href="/uploads/<?= $task['file'] ?>"><?= $task['file'] ?></a>
                            </td>

                            <td class="task__date"><?= $task['date_final'] ?></td>
                        </tr>
                    <? endif;

                }
            } ?>
    </table>
</main>
