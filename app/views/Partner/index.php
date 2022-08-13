<main class="flex-shrink-0">
    <div class="container">
        <div class="d-flex justify-content-between">
            <h1 class="mt-1">Список контрагентов</h1>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?=PATH;?>">Главная</a></li>
                <li class="breadcrumb-item"><a type="button" href="<?=PATH;?>/partner/add">Добавить КА</a></li>
                <li class="breadcrumb-item active" aria-current="page">Контрагенты</li>
            </ol>
        </nav>
        <?php /** @var array $partners */
        if($partners): ?>
        <table id="partner_index" class="table display" style="width:100%">
                <thead>
                <tr>
                    <th>Наименование</th>
                    <th>Адрес</th>
                    <th>ИНН</th>
                    <th>КПП</th>
                    <th>ЕР</th>
                    <th>Кредиторка</th>
                    <th>Отсрочка</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($partners as $partner): ?>
                    <tr>
                        <th><a href="partner/<?= $partner['inn'];?>"><?= $partner['name'];?></a></th>
                        <td><?= $partner['address'];?></td>
                        <td><?= $partner['inn'];?></td>
                        <td><?= $partner['kpp'];?></td>
                        <td><?= $partner['er'];?></td>
                        <td><?= number_format($partner['sum'], 2, ',', '&nbsp;');?>&nbsp;₽</td>
                        <td><?= $partner['delay'];?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
        <div class="row text-center p-2">
            <h2>Информации об активных КА не найдено</h2>
        </div>
        <?php endif; ?>
    </div>
</main>
<script type="text/javascript" src="assets/DataTables/datatables.min.js"></script>
<script>
    $(function () {
        $('#partner_index').dataTable( {
        "aLengthMenu": [[8, 15, 25, -1], [8, 15, 25, "All"]],
        "language": {
            "url": "/assets/DataTables/ru.json"
        },
        "aoColumns": [
            null,
            {"bSearchable": false },
            {"sClass": "text-center",
                "bSearchable": false },
            {"sClass": "text-center",
                "bSearchable": false },
            {"sClass": "text-center",
                "bSearchable": false },
            {"sClass": "text-center",
                "bSearchable": false },
            {"sClass": "text-center",
                "bSearchable": false },

        ]
        });
    });
</script>
