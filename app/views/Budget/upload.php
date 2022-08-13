<main role="main" class="flex-shrink-0">
    <div class="container">
        <div class="d-flex justify-content-between">
            <h1 class="mt-1">Загрузка новых бюджетных операций</h1>
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?=PATH;?>">Главная</a></li>
                <li class="breadcrumb-item "><a href="<?=PATH;?>/budget">Бюджетные операции</a></li>
                <li class="breadcrumb-item active" aria-current="page">Загрузка данных</li>
            </ol>
        </nav>
        <!--<form action="/target" class="dropzone" id="my-great-dropzone"></form>-->
        <!--<form action="" class="dropzone"></form>-->
        <!--<div class="my-dropzone"></div>-->
        <form action="budget/upload" id="upload_file" class="was-validated" method="post" enctype="multipart/form-data">
            <div class="col-12 has-feedback">
                <div id="upload" class="upload"></div>
                <!--<label for="file">Выберите файл для загрузки</label>
                <input type="file" name="file" id="file" class="form-control" aria-label="file example" required>-->
            </div>
            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary mt-3 mb-3">Загрузить</button>
            </div>
        </form>
        <?php if (isset($_SESSION['success'])) : ?>
            <div class="alert alert-success" role="alert">
                <?=$_SESSION['success'];unset($_SESSION['success']);?>
            </div>
        <?php endif;  ?>
        <?php if (isset($_SESSION['error'])) : ?>
            <div class="alert alert-danger" role="alert">
                <?=$_SESSION['error'];unset($_SESSION['error']);?>
            </div>
        <?php endif;  ?>
    </div>
</main>
<script type="text/javascript" src="assets/Dropzone/dropzone.js"></script>
<script>
    $(function () {
        let myDropzone = new Dropzone('div#upload', {
            paramName: "file",
            url: "/budget/upload-file",
            maxFiles: 1,
            //acceptedFiles: '.jpg, .png',
            success: function (file, responce) {
                console.log(file);
                console.log(responce);
            },
            init: function () {
                $(this.element).html(this.options.dictDefaultMessage);
            },
            processing: function () {
                $('.dz-message').remove();
            },
            dictDefaultMessage: '<div class="dz-message">Нажмите здесь или перетащите сюда файлы для загрузки</div>',
            dictMaxFilesExceeded: 'Достигнут лимит загрузки файлов - разрешено {{maxFiles}}',
        });
    });
</script>
