<section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>

    <nav class="main-navigation">
        <ul class="main-navigation__list">
            <?php foreach ($projects as $project): ?>
            <li class="main-navigation__list-item">
                <a class="main-navigation__list-item-link <?= getClassForMenuItem($project) ?>"
                    href=<?= buildUrlForProject('index.php', ['project_id' => $project['id']]) ?>><?= $project['title'] ?></a>
                <span class="main-navigation__list-item-count"><?= $project['tasks_count'] ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <a class="button button--transparent button--plus content__side-button" href="/add-project.php">Добавить проект</a>
</section>

<main class="content__main">
    <h2 class="content__main-heading">Добавление проекта</h2>

    <form class="form" action="/add-project.php" method="post" autocomplete="off">
        <div class="form__row">
            <label class="form__label" for="project_name">Название <sup>*</sup></label>

            <input class="form__input <?= $class ?? '' ?>" type="text" name="name" id="project_name"
                value="<?= $post['name'] ?? NULL ?>" placeholder="Введите название проекта">

            <p class="form__message"><?= $errors ?? '' ?></p>
        </div>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="" value="Добавить">
        </div>
    </form>
</main>
