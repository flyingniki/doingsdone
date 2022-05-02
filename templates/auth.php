<section class="content__side">
    <p class="content__side-info">Если у вас уже есть аккаунт, авторизуйтесь на сайте</p>

    <a class="button button--transparent content__side-button" href="form-authorization.html">Войти</a>
</section>

<main class="content__main">
    <h2 class="content__main-heading">Вход на сайт</h2>

    <form class="form" action="/auth.php" method="post" autocomplete="off">
        <div class="form__row">
            <label class="form__label" for="email">E-mail <sup>*</sup></label>

            <input class="form__input <?= $class['email'] ?? '' ?>" type="text" name="email" id="email"
                value="<?= $post['email'] ?? NULL ?>" placeholder="Введите e-mail">

            <p class="form__message"><?= $errors['email'] ?? '' ?></p>
        </div>

        <div class="form__row">
            <label class="form__label" for="password">Пароль <sup>*</sup></label>

            <input class="form__input <?= $class['password'] ?? '' ?>" type="password" name="password" id="password"
                value="<?= $post['password'] ?? NULL ?>" placeholder="Введите пароль">

            <p class="form__message"><?= $errors['password'] ?? '' ?></p>
        </div>

        <div class="form__row form__row--controls">
            <input class="button" type="submit" name="" value="Войти">
        </div>
    </form>

</main>
