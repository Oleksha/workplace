<?php
/*session_start();
if ($_SESSION['user']) {
    header('Location: /');
}
*/?>

<!-- Форма регистрации -->

<form action="/user/signup" method="post" enctype="multipart/form-data" class="was-validated">
    <div class="form-floating mb-3">
        <input type="text" name="name" class="form-control" id="name" placeholder="Иванов Иван Иванович" value="<?=$_SESSION['form_data']['name'] ?? ''?>" required>
        <label for="name">Полное имя</label>
    </div>
    <div class="form-floating mb-3">
        <input type="text" name="login" class="form-control" id="login" placeholder="ivan" value="<?=$_SESSION['form_data']['login'] ?? ''?>" required>
        <label for="login">Ваш логин</label>
    </div>
    <div class="form-floating mb-3">
        <input type="email" name="email" class="form-control" id="email" placeholder="name@example.com" value="<?=$_SESSION['form_data']['email'] ?? ''?>" required>
        <label for="email">Адрес электронной почты</label>
    </div>
    <div class="mb-3">
        <input type="file" name="avatar" class="form-control" id="avatar">
    </div>
    <div class="form-floating mb-3">
        <input type="password" name="password" class="form-control" id="password" placeholder="" required>
        <label for="password">Введите пароль</label>
    </div>
    <div class="form-floating mb-3">
        <input type="password" name="password_confirm" class="form-control" id="password_confirm" placeholder="" required>
        <label for="password_confirm">Подтвердите пароль</label>
    </div>
    <div class="text-center">
        <button class="btn btn-primary mb-1" type="submit">Зарегистрироваться</button>
    </div>
    <p class="text-center">У вас уже есть аккаунт? <a href="/user/login">Авторизируйтесь</a></p>
    <?php if (isset($_SESSION['errors'])) : ?>
        <div class="alert alert-danger"><?php  echo $_SESSION['errors']; unset($_SESSION['errors']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])) : ?>
        <div class="alert alert-success"><?php  echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
</form>
<?php if (isset($_SESSION['form_data'])) unset($_SESSION['form_data']); ?>