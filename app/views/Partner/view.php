<main class="content">
    <div class="container">
        <h1 class="mt-1">Карточка контрагента</h1>
        <?php if (!empty($partner)) : ?>
            <nav aria-label="breadcrumb">
                <ol style="--bs-breadcrumb-divider: '>';" class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?=PATH;?>">Главная</a></li>
                    <li class="breadcrumb-item"><a href="<?=PATH;?>/partner">Список контрагентов</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?=
                        $partner['name']; ?></li>
                </ol>
            </nav>
            <h2><?=$partner['name']; ?></h2>
            <div class="accordion" id="accordionPartner">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            Приходы
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionPartner">
                        <div class="accordion-body">
                            <?php if(!empty($receipt)): ?>
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="receipt-tab" data-bs-toggle="tab" data-bs-target="#receipt-tab-pane" type="button" role="tab" aria-controls="receipt-tab-pane" aria-selected="true">Неоплаченные</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment-tab-pane" type="button" role="tab" aria-controls="payment-tab-pane" aria-selected="true">Оплаченные</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-tab-pane" type="button" role="tab" aria-controls="all-tab-pane" aria-selected="true">Все</button>
                                    </li>
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="receipt-tab-pane" role="tabpanel" aria-labelledby="receipt-tab">
                                        <div class="row p-3">
                                            <table class="table display" id="receiptsPartner">
                                                <thead>
                                                <tr class="table-active text-center">
                                                    <th scope="col" class="h-100 align-middle">Номер</th>
                                                    <th scope="col" class="h-100 align-middle">Дата</th>
                                                    <th scope="col" class="h-100 align-middle">Сумма</th>
                                                    <th scope="col" class="h-100 align-middle">Документ</th>
                                                    <th scope="col" class="h-100 align-middle">Дата док.</th>
                                                    <th scope="col" class="h-100 align-middle">Примечание</th>
                                                    <th scope="col" class="h-100 align-middle">Оплата</th>
                                                    <th scope="col" class="h-100 align-middle">Действия</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $pay_no = 0; ?>
                                                <?php foreach ($receipt as $item): ?>
                                                    <?php if (is_null($item['date_pay'])) : ?>
                                                        <tr>
                                                            <th class="text-center h-100 align-middle" scope="row"><a href="#" class="edit-receipt-link" data-toggle="tooltip" data-placement="top" title="Редактировать" data-id="<?= $item['id'];?>" data-bs-toggle="modal" data-bs-target="#editReceiptModal"><?= $item['number'];?></a></th>
                                                            <td class="text-center h-100 align-middle"><?= $item['date'];?></td>
                                                            <td class="text-center h-100 align-middle"><?= number_format($item['sum'], 2, ',', '&nbsp;');?>&nbsp;₽</td>
                                                            <td class="text-center h-100 align-middle"><?= $item['num_doc'];?></td>
                                                            <td class="text-center h-100 align-middle"><?= $item['date_doc'];?></td>
                                                            <td class="h-100 align-middle"><?= $item['note'];?></td>
                                                            <td class="text-center h-100 align-middle"><?= $item['date_pay'];?></td>
                                                            <?php
                                                            $title = '';
                                                            if ($item['type'] == 1) {
                                                                $title = 'Просмотреть';
                                                                $svg = '<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-zoom-out" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/>
                                                                        <path d="M10.344 11.742c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1 6.538 6.538 0 0 1-1.398 1.4z"/>
                                                                    </svg>';
                                                            } elseif ($item['type'] == 2) {
                                                                $title = 'Изменить';
                                                                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                                                            <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"></path>
                                                                        </svg>';
                                                            } elseif ($item['type'] == 3) {
                                                                $title = 'Оплатить';
                                                                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cash-coin" viewBox="0 0 16 16">
                                                                        <path fill-rule="evenodd" d="M11 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm5-4a5 5 0 1 1-10 0 5 5 0 0 1 10 0z" fill="green"/>
                                                                        <path d="M9.438 11.944c.047.596.518 1.06 1.363 1.116v.44h.375v-.443c.875-.061 1.386-.529 1.386-1.207 0-.618-.39-.936-1.09-1.1l-.296-.07v-1.2c.376.043.614.248.671.532h.658c-.047-.575-.54-1.024-1.329-1.073V8.5h-.375v.45c-.747.073-1.255.522-1.255 1.158 0 .562.378.92 1.007 1.066l.248.061v1.272c-.384-.058-.639-.27-.696-.563h-.668zm1.36-1.354c-.369-.085-.569-.26-.569-.522 0-.294.216-.514.572-.578v1.1h-.003zm.432.746c.449.104.655.272.655.569 0 .339-.257.571-.709.614v-1.195l.054.012z" fill="green"/>
                                                                        <path d="M1 0a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h4.083c.058-.344.145-.678.258-1H3a2 2 0 0 0-2-2V3a2 2 0 0 0 2-2h10a2 2 0 0 0 2 2v3.528c.38.34.717.728 1 1.154V1a1 1 0 0 0-1-1H1z" fill="green"/>
                                                                        <path d="M9.998 5.083 10 5a2 2 0 1 0-3.132 1.65 5.982 5.982 0 0 1 3.13-1.567z" fill="green"/>
                                                                    </svg>';
                                                            }
                                                            ?>
                                                            <td class="text-center h-100 align-middle">
                                                                <a type="button" href="partner/payment?receipt=<?= $item['id'];?>&type=<?= $item['type'];?>" class="btn btn-outline-primary btn-sm my-btn" data-toggle="tooltip" data-placement="top" title="<?= $title; ?>">
                                                                    <?= $svg; ?>
                                                                </a>
                                                                <a type="button" class="btn btn-outline-primary btn-sm my-btn" data-toggle="tooltip" data-placement="top" title="Удалить" href="receipt/del?id=<?= $item['id'];?>" onclick="return window.confirm('OK?');">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                                        <path fill-rule="evenodd" d="M13.854 2.146a.5.5 0 0 1 0 .708l-11 11a.5.5 0 0 1-.708-.708l11-11a.5.5 0 0 1 .708 0Z" fill="red"/>
                                                                        <path fill-rule="evenodd" d="M2.146 2.146a.5.5 0 0 0 0 .708l11 11a.5.5 0 0 0 .708-.708l-11-11a.5.5 0 0 0-.708 0Z" fill="red"/>
                                                                    </svg>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <?php $pay_no += 1; ?>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                                <?php if ($pay_no == 0) : ?>
                                                    <tr>
                                                        <td colspan="8" class="text-center">Информации о неоплаченных приходах нет</td>
                                                    </tr>
                                                <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="payment-tab-pane" role="tabpanel" aria-labelledby="payment-tab">
                                        <div class="row p-3">
                                            <table class="table display" id="paymentPartner">
                                                <thead>
                                                <tr class="table-active text-center">
                                                    <th scope="col" class="h-100 align-middle">Номер</th>
                                                    <th scope="col" class="h-100 align-middle">Дата</th>
                                                    <th scope="col" class="h-100 align-middle">Сумма</th>
                                                    <th scope="col" class="h-100 align-middle">Документ</th>
                                                    <th scope="col" class="h-100 align-middle">Дата док.</th>
                                                    <th scope="col" class="h-100 align-middle">Примечание</th>
                                                    <th scope="col" class="h-100 align-middle">Оплата</th>
                                                    <th scope="col" class="h-100 align-middle">Действия</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php $pay_yes = 0; ?>
                                                <?php foreach ($receipt as $item): ?>
                                                    <?php if (!is_null($item['date_pay'])) : ?>
                                                        <tr>
                                                            <th class="text-center h-100 align-middle" scope="row"><a href="#" class="edit-receipt-link" data-toggle="tooltip" data-placement="top" title="Редактировать" data-id="<?= $item['id'];?>" data-bs-toggle="modal" data-bs-target="#editReceiptModal"><?= $item['number'];?></a></th>
                                                            <td class="text-center h-100 align-middle"><?= $item['date'];?></td>
                                                            <td class="text-center h-100 align-middle"><?= number_format($item['sum'], 2, ',', '&nbsp;');?>&nbsp;₽</td>
                                                            <td class="text-center h-100 align-middle"><?= $item['num_doc'];?></td>
                                                            <td class="text-center h-100 align-middle"><?= $item['date_doc'];?></td>
                                                            <td class="h-100 align-middle"><?= $item['note'];?></td>
                                                            <td class="text-center h-100 align-middle"><?= $item['date_pay'];?></td>
                                                            <?php
                                                            $title = '';
                                                            if ($item['type'] == 1) {
                                                                $title = 'Просмотреть';
                                                                $svg = '<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-zoom-out" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/>
                                                                        <path d="M10.344 11.742c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1 6.538 6.538 0 0 1-1.398 1.4z"/>
                                                                    </svg>';
                                                            } elseif ($item['type'] == 2) {
                                                                $title = 'Изменить';
                                                                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                                                            <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"></path>
                                                                        </svg>';
                                                            } elseif ($item['type'] == 3) {
                                                                $title = 'Оплатить';
                                                                $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cash-coin" viewBox="0 0 16 16">
                                                                        <path fill-rule="evenodd" d="M11 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm5-4a5 5 0 1 1-10 0 5 5 0 0 1 10 0z" fill="green"/>
                                                                        <path d="M9.438 11.944c.047.596.518 1.06 1.363 1.116v.44h.375v-.443c.875-.061 1.386-.529 1.386-1.207 0-.618-.39-.936-1.09-1.1l-.296-.07v-1.2c.376.043.614.248.671.532h.658c-.047-.575-.54-1.024-1.329-1.073V8.5h-.375v.45c-.747.073-1.255.522-1.255 1.158 0 .562.378.92 1.007 1.066l.248.061v1.272c-.384-.058-.639-.27-.696-.563h-.668zm1.36-1.354c-.369-.085-.569-.26-.569-.522 0-.294.216-.514.572-.578v1.1h-.003zm.432.746c.449.104.655.272.655.569 0 .339-.257.571-.709.614v-1.195l.054.012z" fill="green"/>
                                                                        <path d="M1 0a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h4.083c.058-.344.145-.678.258-1H3a2 2 0 0 0-2-2V3a2 2 0 0 0 2-2h10a2 2 0 0 0 2 2v3.528c.38.34.717.728 1 1.154V1a1 1 0 0 0-1-1H1z" fill="green"/>
                                                                        <path d="M9.998 5.083 10 5a2 2 0 1 0-3.132 1.65 5.982 5.982 0 0 1 3.13-1.567z" fill="green"/>
                                                                    </svg>';
                                                            }
                                                            ?>
                                                            <td class="text-center h-100 align-middle">
                                                                <a type="button" href="partner/payment?receipt=<?= $item['id'];?>&type=<?= $item['type'];?>" class="btn btn-outline-primary btn-sm my-btn" data-toggle="tooltip" data-placement="top" title="<?= $title; ?>">
                                                                    <?= $svg; ?>
                                                                </a>
                                                                <a type="button" class="btn btn-outline-primary btn-sm my-btn" data-toggle="tooltip" data-placement="top" title="Удалить" href="receipt/del?id=<?= $item['id'];?>" onclick="return window.confirm('OK?');">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                                        <path fill-rule="evenodd" d="M13.854 2.146a.5.5 0 0 1 0 .708l-11 11a.5.5 0 0 1-.708-.708l11-11a.5.5 0 0 1 .708 0Z" fill="red"/>
                                                                        <path fill-rule="evenodd" d="M2.146 2.146a.5.5 0 0 0 0 .708l11 11a.5.5 0 0 0 .708-.708l-11-11a.5.5 0 0 0-.708 0Z" fill="red"/>
                                                                    </svg>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <?php $pay_yes += 1; ?>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                                <?php if ($pay_yes == 0) : ?>
                                                    <tr>
                                                        <td colspan="8" class="text-center">Информации об оплаченных приходах нет</td>
                                                    </tr>
                                                <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                    <div class="tab-pane fade" id="all-tab-pane" role="tabpanel" aria-labelledby="all-tab">
                                        <div class="row p-3">
                                            <table class="table display" id="allPartner">
                                                <thead>
                                                <tr class="table-active text-center">
                                                    <th scope="col" class="h-100 align-middle">Номер</th>
                                                    <th scope="col" class="h-100 align-middle">Дата</th>
                                                    <th scope="col" class="h-100 align-middle">Сумма</th>
                                                    <th scope="col" class="h-100 align-middle">Документ</th>
                                                    <th scope="col" class="h-100 align-middle">Дата док.</th>
                                                    <th scope="col" class="h-100 align-middle">Примечание</th>
                                                    <th scope="col" class="h-100 align-middle">Оплата</th>
                                                    <th scope="col" class="h-100 align-middle">Действия</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach ($receipt as $item): ?>
                                                    <tr>
                                                        <th class="text-center h-100 align-middle" scope="row"><a href="#" class="edit-receipt-link" data-toggle="tooltip" data-placement="top" title="Редактировать" data-id="<?= $item['id'];?>" data-bs-toggle="modal" data-bs-target="#editReceiptModal"><?= $item['number'];?></a></th>
                                                        <td class="text-center h-100 align-middle"><?= $item['date'];?></td>
                                                        <td class="text-center h-100 align-middle"><?= number_format($item['sum'], 2, ',', '&nbsp;');?>&nbsp;₽</td>
                                                        <td class="text-center h-100 align-middle"><?= $item['num_doc'];?></td>
                                                        <td class="text-center h-100 align-middle"><?= $item['date_doc'];?></td>
                                                        <td class="h-100 align-middle"><?= $item['note'];?></td>
                                                        <td class="text-center h-100 align-middle"><?= $item['date_pay'];?></td>
                                                        <?php
                                                        $title = '';
                                                        if ($item['type'] == 1) {
                                                            $title = 'Просмотреть';
                                                            $svg = '<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-zoom-out" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/>
                                                                        <path d="M10.344 11.742c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1 6.538 6.538 0 0 1-1.398 1.4z"/>
                                                                    </svg>';
                                                        } elseif ($item['type'] == 2) {
                                                            $title = 'Изменить';
                                                            $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                                                            <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"></path>
                                                                        </svg>';
                                                        } elseif ($item['type'] == 3) {
                                                            $title = 'Оплатить';
                                                            $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cash-coin" viewBox="0 0 16 16">
                                                                        <path fill-rule="evenodd" d="M11 15a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm5-4a5 5 0 1 1-10 0 5 5 0 0 1 10 0z" fill="green"/>
                                                                        <path d="M9.438 11.944c.047.596.518 1.06 1.363 1.116v.44h.375v-.443c.875-.061 1.386-.529 1.386-1.207 0-.618-.39-.936-1.09-1.1l-.296-.07v-1.2c.376.043.614.248.671.532h.658c-.047-.575-.54-1.024-1.329-1.073V8.5h-.375v.45c-.747.073-1.255.522-1.255 1.158 0 .562.378.92 1.007 1.066l.248.061v1.272c-.384-.058-.639-.27-.696-.563h-.668zm1.36-1.354c-.369-.085-.569-.26-.569-.522 0-.294.216-.514.572-.578v1.1h-.003zm.432.746c.449.104.655.272.655.569 0 .339-.257.571-.709.614v-1.195l.054.012z" fill="green"/>
                                                                        <path d="M1 0a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h4.083c.058-.344.145-.678.258-1H3a2 2 0 0 0-2-2V3a2 2 0 0 0 2-2h10a2 2 0 0 0 2 2v3.528c.38.34.717.728 1 1.154V1a1 1 0 0 0-1-1H1z" fill="green"/>
                                                                        <path d="M9.998 5.083 10 5a2 2 0 1 0-3.132 1.65 5.982 5.982 0 0 1 3.13-1.567z" fill="green"/>
                                                                    </svg>';
                                                        }
                                                        ?>
                                                        <td class="text-center h-100 align-middle">
                                                            <a type="button" href="partner/payment?receipt=<?= $item['id'];?>&type=<?= $item['type'];?>" class="btn btn-outline-primary btn-sm my-btn" data-toggle="tooltip" data-placement="top" title="<?= $title; ?>">
                                                                <?= $svg; ?>
                                                            </a>
                                                                <a type="button" class="btn btn-outline-primary btn-sm my-btn" data-toggle="tooltip" data-placement="top" title="Удалить" href="receipt/del?id=<?= $item['id'];?>" onclick="return window.confirm('OK?');">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                                        <path fill-rule="evenodd" d="M13.854 2.146a.5.5 0 0 1 0 .708l-11 11a.5.5 0 0 1-.708-.708l11-11a.5.5 0 0 1 .708 0Z" fill="red"/>
                                                                        <path fill-rule="evenodd" d="M2.146 2.146a.5.5 0 0 0 0 .708l11 11a.5.5 0 0 0 .708-.708l-11-11a.5.5 0 0 0-.708 0Z" fill="red"/>
                                                                    </svg>
                                                                </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php else : ?>
                                <h3>Данные о приходах отсутствуют</h3>
                            <?php endif; ?>
                            <div class="d-flex justify-content-center">
                                <a type="button" class="btn btn-outline-primary mt-3 add-receipt-link" data-id="<?= $partner['id'];?>" data-bs-toggle="modal" data-bs-target="#addReceiptModal">Добавить новый приход</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Единоличные решения
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionPartner">
                        <div class="accordion-body">
                            <ul class="nav nav-tabs" id="erTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="current-tab" data-bs-toggle="tab" data-bs-target="#current-tab-pane" type="button" role="tab" aria-controls="current-tab-pane" aria-selected="true">Действующие</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="end-tab" data-bs-toggle="tab" data-bs-target="#end-tab-pane" type="button" role="tab" aria-controls="end-tab-pane" aria-selected="false">Остальные</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="current-tab-pane" role="tabpanel" aria-labelledby="current-tab" tabindex="0">
                                    <div class="row p-3">
                                        <?php if(!empty($ers)): ?>
                                            <?php foreach ($ers as $er): ?>
                                                <?php
                                                $date = strtotime('+1 month'); // текущая дата +1 месяц
                                                $selector = 'alert alert-success';
                                                $class_date = '';
                                                $class_sum = '';
                                                if ($er['data_end'] < date('Y-m-d', $date))
                                                {
                                                    $selector = 'alert alert-warning';
                                                    $class_date = 'class="alert-warning"';
                                                }
                                                if (($er['summa'] - $er['costs']) < ($er['summa'] * 0.03))
                                                {
                                                    $selector = 'alert alert-warning';
                                                }
                                                if (($er['summa'] - $er['costs']) <= 0)
                                                {
                                                    $selector = 'alert alert-danger';
                                                }?>
                                                <div class="row align-items-center" id="erPartner">
                                                    <div class="col-12">
                                                        <div class="<?= $selector; ?>" role="alert">
                                                            <?= $er['name_budget_item'];?> - <b><i><?= $er['number'];?></i></b> - Осталось: <b><?= number_format($er['summa'] - $er['costs'], 2, ',', '&nbsp;');?>&nbsp;₽</b>
                                                        </div>
                                                        <div class="row d-flex align-items-center">
                                                            <div class="input-group mb-3">
                                                                <span class="input-group-text">Период действия:</span>
                                                                <input type="text" aria-label="ИНН" class="form-control" value="<?= $er['data_start'];?> / <?= $er['data_end'];?>" disabled>
                                                                <span class="input-group-text">Cумма:</span>
                                                                <input type="text" aria-label="ИНН" class="form-control" value="<?= number_format($er['summa'], 2, ',', '&nbsp;');?>&nbsp;₽" disabled>
                                                                <button class="btn btn-outline-success view-er-link" type="button" id="button-addon1" data-id="<?= $er['id'];?>" data-toggle="tooltip" data-placement="top" title="Просмотреть" data-bs-toggle="modal" data-bs-target="#viewERModal">
                                                                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-zoom-out" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/>
                                                                        <path d="M10.344 11.742c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1 6.538 6.538 0 0 1-1.398 1.4z"/>
                                                                    </svg></button>
                                                                <button class="btn btn-outline-primary edit-er-link" data-id="<?= $er['id'];?>" type="button" id="button-addon2"  data-toggle="tooltip" data-placement="top" title="Изменить" data-bs-toggle="modal" data-bs-target="#editERModal">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                                                        <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"></path>
                                                                    </svg></button>
                                                                <a class="btn btn-outline-danger del-er-link" type="button" id="button-addon3" data-toggle="tooltip" data-placement="top" title="Удалить" href="er/del?id=<?= $er['id'];?>" onclick="return window.confirm('OK?');">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                                        <path fill-rule="evenodd" d="M13.854 2.146a.5.5 0 0 1 0 .708l-11 11a.5.5 0 0 1-.708-.708l11-11a.5.5 0 0 1 .708 0Z"/>
                                                                        <path fill-rule="evenodd" d="M2.146 2.146a.5.5 0 0 0 0 .708l11 11a.5.5 0 0 0 .708-.708l-11-11a.5.5 0 0 0-.708 0Z"/>
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                            <hr>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="alert alert-danger" role="alert">Нет действующих единоличных решений</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="end-tab-pane" role="tabpanel" aria-labelledby="end-tab" tabindex="0">
                                    <div class="row p-3">
                                        <?php if(!empty($diff)): ?>
                                            <?php foreach ($diff as $er): ?>
                                                <div class="row align-items-center" id="erPartner">
                                                    <div class="col-12">
                                                        <div class="alert alert-danger" role="alert">
                                                            <?= $er['name_budget_item'];?> - <b><i><?= $er['number'];?></i></b>
                                                        </div>
                                                        <div class="row d-flex align-items-center">
                                                            <div class="input-group mb-3">
                                                                <span class="input-group-text">Период действия:</span>
                                                                <input type="text" aria-label="ИНН" class="form-control" value="<?= $er['data_start'];?> / <?= $er['data_end'];?>" disabled>
                                                                <span class="input-group-text">Cумма:</span>
                                                                <input type="text" aria-label="ИНН" class="form-control" value="<?= number_format($er['summa'], 2, ',', '&nbsp;');?>&nbsp;₽" disabled>
                                                                <button class="btn btn-outline-success view-er-link" type="button" id="button-addon1" data-id="<?= $er['id'];?>" data-toggle="tooltip" data-placement="top" title="Просмотреть" data-bs-toggle="modal" data-bs-target="#viewERModal">
                                                                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-zoom-out" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/>
                                                                        <path d="M10.344 11.742c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1 6.538 6.538 0 0 1-1.398 1.4z"/>
                                                                    </svg></button>
                                                                <button class="btn btn-outline-primary edit-er-link" data-id="<?= $er['id'];?>" type="button" id="button-addon2"  data-toggle="tooltip" data-placement="top" title="Изменить" data-bs-toggle="modal" data-bs-target="#editERModal">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                                                        <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"></path>
                                                                    </svg></button>
                                                                <a class="btn btn-outline-danger del-er-link" type="button" id="button-addon3" data-toggle="tooltip" data-placement="top" title="Удалить" href="er/del?id=<?= $er['id'];?>" onclick="return window.confirm('OK?');">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                                                                        <path fill-rule="evenodd" d="M13.854 2.146a.5.5 0 0 1 0 .708l-11 11a.5.5 0 0 1-.708-.708l11-11a.5.5 0 0 1 .708 0Z"/>
                                                                        <path fill-rule="evenodd" d="M2.146 2.146a.5.5 0 0 0 0 .708l11 11a.5.5 0 0 0 .708-.708l-11-11a.5.5 0 0 0-.708 0Z"/>
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                            <hr>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="alert alert-danger" role="alert"> У КА не было единоличных решений</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center">
                                <a type="button" href="er/add" class="btn btn-outline-primary add-er-link" data-id="<?= $partner['id'];?>" data-bs-toggle="modal" data-bs-target="#addERModal">Добавить новое ЕР</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Юридическая информация
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionPartner">
                        <div class="accordion-body" id="cartPartner">
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="basic-addon1">Юридический адрес</span>
                                <input type="text" class="form-control" aria-label="Юридический адрес" aria-describedby="basic-addon1" value="<?php echo $partner['address'] ?? 'Нет данных'; ?>" disabled>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text">ИНН</span>
                                <input type="text" aria-label="ИНН" class="form-control" value="<?php echo $partner['inn'] ?? 'Нет данных'; ?>" disabled>
                                <span class="input-group-text">КПП</span>
                                <input type="text" aria-label="ИНН" class="form-control" value="<?php echo $partner['kpp'] ?? 'Нет данных'; ?>" disabled>
                                <span class="input-group-text">E-mail</span>
                                <input type="text" aria-label="КПП" class="form-control" value="<?php echo $partner['email'] ?? 'Нет данных'; ?>" disabled>
                                <?php if ($partner['email']): ?>
                                    <a href="mailto:<?= $partner['email']; ?>" class="btn btn-outline-secondary" type="button" id="button-addon2">Написать письмо</a>
                                <?php endif; ?>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text">Банк</span>
                                <input type="text" aria-label="ИНН" class="form-control" value='<?php echo $partner['bank'] ?? 'Нет данных'; ?>' disabled>
                            </div>
                            <div class="input-group mb-3">
                                <span class="input-group-text">Расчетный счет</span>
                                <input type="text" aria-label="ИНН" class="form-control" value="<?php echo $partner['account'] ?? 'Нет данных'; ?>" disabled>
                                <span class="input-group-text">БИК</span>
                                <input type="text" aria-label="ИНН" class="form-control" value="<?php echo $partner['bic'] ?? 'Нет данных'; ?>" disabled>
                            </div>
                            <div class="d-flex justify-content-center">
                                <a type="button" class="btn btn-outline-primary edit-ka-link" data-id="<?= $partner['id'];?>" data-bs-toggle="modal" data-bs-target="#editKAModal">Редактировать</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <h2>Упс. Почему-то ничего не нашлось...</h2>
        <?php endif; ?>
    </div>
</main>

<div id="editKAModal" class="modal fade" tabindex="-1" data-bs-backdrop="static" aria-labelledby="editKALabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Редактирование данных юридического лица</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <form method="post" action="partner/edit-partner" id="partner_edit" class="was-validated">
                <div class="row g-3 modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary">Записать</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="editERModal" class="modal fade" tabindex="-1" data-bs-backdrop="static" aria-labelledby="editERLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Редактирование данных ЕР</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <form method="post" action="er/edit-er" id="er_edit" role="form" class="was-validated">
                <div class="row g-3 modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary">Записать</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="viewERModal" class="modal fade" tabindex="-1" data-bs-backdrop="static" aria-labelledby="viewERLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Расходы по ЕР</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div id="addERModal" class="modal fade" tabindex="-1" data-bs-backdrop="static" aria-labelledby="addERLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Добавление нового ЕР</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <form method="post" action="er/add-er" id="er_add" role="form" class="was-validated">
                <div class="row g-3 modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="addReceiptModal" class="modal fade" tabindex="-1" data-bs-backdrop="static" aria-labelledby="addReceiptLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Добавление нового прихода</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <form method="post" action="receipt/add-receipt" id="receipt_add" role="form" class="was-validated">
                <div class="row g-3 modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="editReceiptModal" class="modal fade" tabindex="-1" data-bs-backdrop="static" aria-labelledby="editReceiptLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Редактирование данных прихода</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <form method="post" action="receipt/edit-receipt" id="receipt_add" role="form" class="was-validated">
                <div class="row g-3 modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    $(function () {
        $('body').on('click', '.add-receipt-link', function (e) {
            e.preventDefault(); // отменяем действие по умолчанию для ссылки или кнопки
            // получаем необходимые нам данные
            let id = $(this).data('id'); // идентификатор КА

            // отправляем стандартный аякс запрос на сервер
            $.ajax({
                url: '/receipt/add', // всегда указываем от корня
                data: {id: id}, // передаем данные
                type: 'GET', // тип передаваемого запроса
                success: function (res) {
                    // если данные получены
                    showAddReceipt(res);
                },
                error: function () {
                    // если данных нет или запрос не дошел
                    alert('Ошибка получения данных с сервера! Попробуйте позже.');
                }
            });
        });
        $('body').on('click', '.edit-receipt-link', function (e) {
            e.preventDefault(); // отменяем действие по умолчанию для ссылки или кнопки
            // получаем необходимые нам данные
            let id = $(this).data('id'); // идентификатор прихода

            // отправляем стандартный аякс запрос на сервер
            $.ajax({
                url: '/receipt/edit', // всегда указываем от корня
                data: {id: id}, // передаем данные
                type: 'GET', // тип передаваемого запроса
                success: function (res) {
                    // если данные получены
                    showEditReceipt(res);
                },
                error: function () {
                    // если данных нет или запрос не дошел
                    alert('Ошибка получения данных с сервера! Попробуйте позже.');
                }
            });
        });
        function showAddReceipt(receipt) {
            // выводим содержимое страницы
            $('#addReceiptModal .modal-body').html(receipt);
        }
        function showEditReceipt(receipt) {
            // выводим содержимое страницы
            $('#editReceiptModal .modal-body').html(receipt);
        }
        /* Контрагент */
        $('body').on('click', '.edit-ka-link', function (e) {
            e.preventDefault(); // отменяем действие по умолчанию для ссылки или кнопки
            // получаем необходимые нам данные
            let id = $(this).data('id'); // идентификатор КА
            // отправляем стандартный аякс запрос на сервер
            $.ajax({
                url: '/partner/edit', // всегда указываем от корня
                data: {id: id}, // передаем данные
                type: 'GET', // тип передаваемого запроса
                success: function (res) {
                    // если данные получены
                    showEditKa(res);
                },
                error: function () {
                    // если данных нет или запрос не дошел
                    alert('Ошибка получения данных с сервера! Попробуйте позже.');
                }
            });
        });
        function showEditKa(ka) {
            // выводим содержимое страницы
            $('#editKAModal .modal-body').html(ka);
        }
        /* Единоличное решение */
        $('body').on('click', '.edit-er-link', function (e) {
            e.preventDefault(); // отменяем действие по умолчанию для ссылки или кнопки
            // получаем необходимые нам данные
            let id = $(this).data('id') // идентификатор ЕР
            // отправляем стандартный аякс запрос на сервер
            $.ajax({
                url: '/er/edit', // всегда указываем от корня
                data: {id: id}, // передаем данные
                type: 'GET', // тип передаваемого запроса
                success: function (res) {
                    // если данные получены
                    showEr(res);
                },
                error: function () {
                    // если данных нет или запрос не дошел
                    alert('Ошибка получения данных с сервера! Попробуйте позже.');
                }
            });
        });
        $('body').on('click', '.add-er-link', function (e) {
            e.preventDefault(); // отменяем действие по умолчанию для ссылки или кнопки
            // получаем необходимые нам данные
            let id = $(this).data('id'); // идентификатор КА
            // отправляем стандартный аякс запрос на сервер
            $.ajax({
                url: '/er/add', // всегда указываем от корня
                data: {id: id}, // передаем данные
                type: 'GET', // тип передаваемого запроса
                success: function (res) {
                    // если данные получены
                    showAddEr(res);
                },
                error: function () {
                    // если данных нет или запрос не дошел
                    alert('Ошибка получения данных с сервера! Попробуйте позже.');
                }
            });
        });
        $('body').on('click', '.view-er-link', function (e) {
            e.preventDefault(); // отменяем действие по умолчанию для ссылки или кнопки
            // получаем необходимые нам данные
            let id = $(this).data('id') // идентификатор ЕР
            // отправляем стандартный аякс запрос на сервер
            $.ajax({
                url: '/er/view', // всегда указываем от корня
                data: {id: id}, // передаем данные
                type: 'GET', // тип передаваемого запроса
                success: function (res) {
                    // если данные получены
                    showViewEr(res);
                },
                error: function () {
                    // если данных нет или запрос не дошел
                    alert('Ошибка получения данных с сервера! Попробуйте позже.');
                }
            });
        });
        function showEr(er) {
            // выводим содержимое страницы
            $('#editERModal .modal-body').html(er);
        }
        function showAddEr(er) {
            // выводим содержимое страницы
            $('#addERModal .modal-body').html(er);
        }
        function showViewEr(er) {
            // выводим содержимое страницы
            $('#viewERModal .modal-body').html(er);
        }
    });
</script>
<script type="text/javascript" src="assets/DataTables/datatables.min.js"></script>
<script>
    $(function () {
        $('#receiptsPartner').dataTable( {
            "ordering": false,
            "aLengthMenu": [[7, 15, 25, -1], [7, 15, 25, "All"]],
            "language": {
                "url": "/assets/DataTables/ru.json"
            }
        });
        $('#paymentPartner').dataTable( {
            "ordering": false,
            "aLengthMenu": [[7, 15, 25, -1], [7, 15, 25, "All"]],
            "language": {
                "url": "/assets/DataTables/ru.json"
            }
        });
        $('#allPartner').dataTable( {
            "ordering": false,
            "aLengthMenu": [[7, 15, 25, -1], [7, 15, 25, "All"]],
            "language": {
                "url": "/assets/DataTables/ru.json"
            }
        });
    });
</script>
