<section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>

    <nav class="main-navigation">
        <ul class="main-navigation__list">
            <?foreach ($projects as $project):?>
                <li class="main-navigation__list-item">
                    <a class="main-navigation__list-item-link <?= getClassForMenuItem($project) ?>" href=<?= buildUrlForProject('index.php', ['project_id' => $project['id']]) ?>><?= $project['title'] ?></a>
                    <span class="main-navigation__list-item-count"><?= $project['tasks_count'] ?></span>
                </li>
            <?endforeach;?>
        </ul>
    </nav>

    <a class="button button--transparent button--plus content__side-button" href="form-project.html">Добавить проект</a>
</section>

<main class="content__main">
    <h2 class="content__main-heading">Добавление задачи</h2>

    <form class="form" action="/add-task.php" method="post" enctype="multipart/form-data" autocomplete="off">
        <div class="form__row">
            <label class="form__label" for="name">Название <sup>*</sup></label>

            <input class="form__input" type="text" name="name" id="name" value="<?= isset($_POST['name']) ? $_POST['name'] : '' ?>" placeholder="Введите название">
        </div>

        <div class="form__row">
            <label class="form__label" for="project">Проект <sup>*</sup></label>

            <select class="form__input form__input--select" name="project_id" id="project">
                <?foreach ($projects as $project):?>
                    <option value="<?= $project['id'] ?>"><?= $project['title'] ?></option>
                <?endforeach;?>
            </select>
        </div>

        <div class="form__row">
            <label class="form__label" for="date">Дата выполнения</label>

            <input class="form__input form__input--date" type="text" name="date" id="date" value="<?= isset($_POST['date']) ? $_POST['date'] : '' ?>"
                placeholder="Введите дату в формате ГГГГ-ММ-ДД">
        </div>

        <div class="form__row">
            <label class="form__label" for="file">Файл</label>

            <div class="form__input-file">
                <input class="visually-hidden" type="file" name="file" id="file" value="<?= $file['name'] ?>">

                <label class="button button--transparent" for="file">
                    <span>Выберите файл</span>
                </label>
            </div>
        </div>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="" value="Добавить">
        </div>
    </form>
</main>