<main role="main" class="flex-shrink-0">
    <div class="container">
        <div class="d-flex justify-content-between">
            <h1 class="mt-1">Список контрагентов</h1>
            <form action="search" method="get" autocomplete="off" class="form-inline mt-2 mt-md-0">
                <input class="form-control mr-sm-2 typeahead" id="typeahead" name="s" type="text" placeholder="Поиск" aria-label="Search">
                <button class="btn btn-outline-info my-2 my-sm-0" type="submit">Поиск</button>
            </form>
        </div>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?=PATH;?>">Главная</a></li>
                <li class="breadcrumb-item"><a type="button" href="<?=PATH;?>/partner/add">Добавить КА</a></li>
                <li class="breadcrumb-item active" aria-current="page">Поиск по запросу "<?= h($query);?>"</li>
            </ol>
        </nav>
        <?php if($partners): ?>
            <table class="table table-striped table-sm">
                <thead>
                <tr class="table-active text-center">
                    <th scope="col" class="h-100 align-middle">Наименование</th>
                    <th scope="col" class="h-100 align-middle">Адрес</th>
                    <th scope="col" class="h-100 align-middle">ИНН</th>
                    <th scope="col" class="h-100 align-middle">КПП</th>
                    <th scope="col" class="h-100 align-middle">Кол-во ЕР</th>
                    <th scope="col" class="h-100 align-middle">Кредиторская<br>задолженность</th>
                    <th scope="col" class="h-100 align-middle">Отсрочка</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($partners as $partner): ?>
                    <tr>
                        <th class="h-100 align-middle" scope="row"><a href="partner/<?= $partner->inn;?>"><?= $partner->name;?></a></th>
                        <td class="h-100 align-middle"><?= $partner->address;?></td>
                        <td class="text-center h-100 align-middle"><?= $partner->inn;?></td>
                        <td class="text-center h-100 align-middle"><?= $partner->kpp;?></td>
                        <td class="text-center h-100 align-middle"><?= $partner->er;?></td>
                        <td class="text-center h-100 align-middle">111&nbsp;199,36&nbsp;₽</td>
                        <td class="text-center h-100 align-middle">30</td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
                <?php if ($pagination->countPages > 1) : ?>
                    <?=$pagination;?>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</main>
