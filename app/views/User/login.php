<!--<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Авторизация и регистрация</title>
    <link rel="stylesheet" href="/css/main.css">
</head>
<body>-->

<!-- Форма авторизации -->

<form action="user/login" method="post" class="was-validated">
    <div class="form-floating mb-3">
        <input type="text" name="login" class="form-control" id="login" placeholder="ivan" required>
        <label for="login">Ваш логин</label>
    </div>
    <div class="form-floating mb-3">
        <input type="password" name="password" class="form-control" id="password" placeholder="pass" required>
        <label for="password">Введите пароль</label>
    </div>
    <div class="text-center">
        <button class="btn btn-primary mb-2" type="submit">Войти</button>
    </div>
    <p class="text-center">У вас нет аккаунта? <a href="/user/signup">Зарегистрируйтесь</a></p>
    <?php if (isset($_SESSION['errors'])) : ?>
        <div class="alert alert-danger"><?php  echo $_SESSION['errors']; unset($_SESSION['errors']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])) : ?>
        <div class="alert alert-success"><?php  echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
</form>
