<!doctype html>
<html lang="ru">
<head>
    <base href="/">
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta charset="UTF-8">
    <link href="assets/bootstrap-5.2.0-dist/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/main.css">
    <?=$this->getMeta();?>
</head>
<body>

    <?=$content;?>

    <script type="text/javascript" src="assets/bootstrap-5.2.0-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let path = '<?=PATH;?>';
    </script>
    <?php
    foreach ($scripts as $script) {
        echo $script;
    }
    ?>
</body>
</html>
