<?php
if (!isset($_SESSION['user'])) {
    header('Location: /user/login');
}
?>
<!DOCTYPE html>
<html lang="ru" class="h-100">
<head>
    <base href="/">
    <link rel="shorcut icon" href="img/star.png" type="image/png" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?=$this->getmeta();?>
    <link href="assets/bootstrap-5.2.0-dist/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="assets/DataTables/datatables.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="assets/chosen/chosen.css">
    <link rel="stylesheet" type="text/css" href="assets/chosen/docsupport/prism.css">
    <link rel="stylesheet" type="text/css" href="assets/Dropzone/dropzone.css">
    <link href="css/style.css" rel="stylesheet" type="text/css">
</head>
<body class="d-flex flex-column h-100">
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <symbol id="grid" fill="#ffffff" viewBox="0 0 16 16">
            <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zM2.5 2a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zM1 10.5A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3zm6.5.5A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3zm1.5-.5a.5.5 0 0 0-.5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 0-.5-.5h-3z"></path>
        </symbol>
    </svg>
    <header class="p-3 bg-dark text-white">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
                    <svg class="bi me-2" width="40" height="32" role="img" aria-label="Grid"><use xlink:href="#grid"></use></svg>
                </a>
                <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                    <li><a href="/" class="nav-link px-2 text-white">Главная</a></li>
                    <li><a href="/partner" class="nav-link px-2 text-white">Контрагенты</a></li>
                    <li><a href="/budget" class="nav-link px-2 text-white">Бюджет</a></li>
                    <li><a href="/convert" class="nav-link px-2 text-white">Конвертирование</a></li>
                </ul>
                <?php if (isset($_SESSION['user'])): ?>
                <?php if ($_SESSION['user']['avatar'] == '') {
                        $img = '/upload/noavatar.png';
                    } else {
                        $img = $_SESSION['user']['avatar'];
                    } ?>
                <div class="dropdown text-end my-style">
                    <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?=$img;?>" alt="<?=$_SESSION['user']['login'];?>" class="rounded-circle" width="32" height="32">
                    </a>
                    <ul class="dropdown-menu text-small" aria-labelledby="dropdownUser1" style="">
                        <li><a class="dropdown-item" href="#">Профиль, <?=$_SESSION['user']['login'];?></a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="user/logout">Выход</a></li>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <div class="content">
        <?= /** @var string $content */
        $content;?>
    </div>
    <footer class="footer mt-auto py-3">
        <div class="container">
            <span class="text-muted">Разработано ИТО ОП "Тольятти"</span>
        </div>
    </footer>

    <div class="preloader"><img src="img/ring.svg" alt=""></div>

    <script>
        let path = '<?=PATH;?>';
    </script>
    <script type="text/javascript" src="assets/chosen/docsupport/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="assets/bootstrap-5.2.0-dist/js/bootstrap.bundle.min.js"></script>
    <?php
        foreach ($scripts as $script) {
            echo $script;
        }
    ?>
    <script type="text/javascript" src="js/main.js?<?echo time();?>"></script>
</body>
</html>
