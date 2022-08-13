$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});

/* Фильтр начало */
$('body').on('change', '.filters select', function () {
    let date_y = $('#select_year option:checked'), // получаем все отмеченные чекбоксы
        date_m = $('#select_month option:checked'), // получаем все отмеченные чекбоксы
        data = '';
    date_y.each(function () {
        // пройдем в цикле по всем отмеченным чекбоксам
        data += this.value + '-'; // добавлем в переменную значения года
    });
    date_m.each(function () {
        // пройдем в цикле по всем отмеченным чекбоксам
        data += this.value + '-01'; // добавлем в переменную значения года
    });
    if (data) {
        // если включен какой либо фильтр
        // формируем AJAX запрос
        $.ajax({
            url: location.href,
            data: {filter: data},
            type: 'GET',
            beforeSend: function () {
                // перед отправкой мы должны включить прелоадер
                $('.preloader').fadeIn(100, function() {
                    // обратимся к классу показывающему продукты
                    // и скроем все отображаемые на экране продукты
                    $('#bo_view').hide();
                });
            },
            success: function (res) {
                // постепенно скроем прелоадер и
                $('.preloader').delay(100).fadeOut('show', function () {
                    // в класс показывающий продукты подгружаем
                    // полученный ответ с сервера и показываем его
                    $('#bo_view').html(res).fadeIn();
                    let url = location.search.replace(/filter(.+?)(&|$)/g, ''); //$2
                    let newURL = location.pathname + url + (location.search ? "&" : "?") + "filter=" + data;
                    newURL = newURL.replace('&&', '&');
                    newURL = newURL.replace('?&', '?');
                    history.pushState({}, '', newURL);
                    location.reload();
                });
            },
            error: function (res) {
                alert('Errors');
            }
        });
    }
    //alert(data);
});
/* Фильтр конец */

/* Поиск */
/*let partners = new Bloodhound ({
    datumTokenizer: Bloodhound.tokenizers.whitespace,
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    remote: {
        wildcard: '%QUERY',
        url: path + '/search/typeahead?query=%QUERY'
    }
});

partners.initialize();

$("#typeahead").typeahead({
    //hint: false,
    highlight: true
},{
    name: 'partners',
    display: 'name',
    limit: 10,
    source: partners
});

$("#typeahead").bind('typeahead:select', function (ev, suggestion) {
    console.log(suggestion);
    window.location = path + '/search/?s=' + encodeURIComponent(suggestion.name);
})*/



$('body').on('click', '.pay-receipt-link', function (e) {
    e.preventDefault(); // отменяем действие по умолчанию для ссылки или кнопки
    // получаем необходимые нам данные
    let id = $(this).data('id'), // идентификатор прихода
        partner = $(this).data('partner'), // имя контрагента
        vat = $(this).data('vat'); // ставка НДС
    // отправляем стандартный аякс запрос на сервер
    $.ajax({
        url: '/receipt/pay', // всегда указываем от корня
        data: {id: id, partner: partner, vat: vat}, // передаем данные
        type: 'GET', // тип передаваемого запроса
        success: function (res) {
            // если данные получены
            showPaymentReceipt(res);
        },
        error: function () {
            // если данных нет или запрос не дошел
            alert('Ошибка получения данных с сервера! Попробуйте позже.');
        }
    });
});


function showPaymentReceipt(receipt) {
    // выводим содержимое страницы
    $('#payReceiptModal .modal-body').html(receipt);
    $('#payReceiptModal').modal();
}

$('body').on('click', '.payment_pay_link', function (e) {
    e.preventDefault(); // отменяем действие по умолчанию для ссылки или кнопки
    // получаем необходимые нам данные
    let id = $(this).data('id_receipt') // идентификатор прихода
    // отправляем стандартный аякс запрос на сервер
    $.ajax({
        url: '/main/pay', // всегда указываем от корня
        data: {id: id}, // передаем данные
        type: 'GET', // тип передаваемого запроса
        success: function (res) {
            // если данные получены
            showPaymentOnly(res);
        },
        error: function () {
            // если данных нет или запрос не дошел
            alert('Ошибка получения данных с сервера! Попробуйте позже.');
        }
    });
});

function showPaymentOnly(receipt) {
    // выводим содержимое страницы
    $('#payModalMain .modal-body').html(receipt);
    $('#payModalMain').modal();
}

$('body').on('click', '.edit-budget-link', function (e) {
    e.preventDefault(); // отменяем действие по умолчанию для ссылки или кнопки
    // получаем необходимые нам данные
    let id = $(this).data('id') // идентификатор прихода
    // отправляем стандартный аякс запрос на сервер
    $.ajax({
        url: '/budget/edit', // всегда указываем от корня
        data: {id: id}, // передаем данные
        type: 'GET', // тип передаваемого запроса
        success: function (res) {
            // если данные получены
            showEditBudget(res);
        },
        error: function () {
            // если данных нет или запрос не дошел
            alert('Ошибка получения данных с сервера! Попробуйте позже.');
        }
    });
});

function showEditBudget(bo) {
    // выводим содержимое страницы
    $('#editBudgetModal .modal-body').html(bo);
    $('#editBudgetModal').modal();
}

// Формирование ЗО на оплату


/*
(function() {
    let app = {

        initialize : function () {
            this.modules();
            this.setUpListeners();
        },

        modules: function () {

        },

        setUpListeners: function () {
            $('form').on('submit', app.submitForm);
        },

        submitForm: function (e) {
            e.preventDefault();
            let form = $('#partner_payment2222');
            app.validateForm(form);
            console.log('Submit!!!');
        },

        validateForm: function (form) {
            let inputs = form.find('input'),
                valid = true;
            inputs.tooltip('hide');
            $.each(inputs, function (index, value) {
                let input = $(value),
                    val = input.val(),
                    formGroup = input.parents('.has-feedback'),
                    label = formGroup.find('label').text().toLowerCase(),
                    textError = 'Введите ' + label;
                if (value.id === 'num_bo') {
                    if (val.length < 5) {
                        console.log('это ' + value.id);
                        input.addClass('is_invalid').removeClass('is_valid');
                        input.tooltip({
                            trigger: 'manual',
                            placement: 'right',
                            tooltip: textError
                        }).tooltip('show');
                        valid = false;
                    } else {
                        input.addClass('is_valid').removeClass('is_invalid');
                    }


                }
            });
        }

    }

    app.initialize();
}());*/
/*
let form = $('#partner_payment');
form.submit( function () {
    let str = $('#num_bo').val();
    //alert();
    if (validateNumBO(str)) {
        $(this).addClass('is_valid').removeClass('is_invalid');
        alert('нормально');
    } else {
        $(this).addClass('is_invalid').removeClass('is_valid');
        alert('Ошибка');
    }
});
function validateNumBO(str) {
    let arr = str.split(';');
    let bo = '';
    let valid = true;
    for(let i = 0; i < arr.length; i++) {
        let my_str = arr[i];
        let result = my_str.match(/CUB[0-9]{10}\/[0-9]+/);
        if (my_str !== result[0]) {
            valid = false;
            break;
        }
    }
    return valid;
};*/





// валидация

    // Пример стартового JavaScript для отключения отправки форм при наличии недопустимых полей
(function() {
        'use strict';
        window.addEventListener('load', function() {
            // Получите все формы, к которым мы хотим применить пользовательские стили проверки Bootstrap
            let forms = document.getElementsByClassName('was-validated');
            // Зацикливайтесь на них и предотвращайте подчинение
            let validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();


  /*  $("#num_bo").on('input', function() {
        //const ids = $('#num_bo').val();
        let str = $(this).val();
        if (validateNumBO(str)) {
            $(this).addClass('is_valid');
            $(this).removeClass('is_invalid');
            //alert('нормально');
        } else {
            $(this).addClass('is_invalid');
            $(this).removeClass('is_valid');
            //alert('Ошибка');
            let arr = str.split(';');
            let bo = '';
            for(let i = 0; i < arr.length; i++) {
                let mystr = arr[i];
                mystr = mystr.trim();
                let result = mystr.match(/CUB[0-9]+/);
                bo = bo + result[0] + '/2022;';
            }
            bo = bo.substring(0, bo.length - 1);
            $('#num_bo').val(bo);
            // /CUB[0-9]+\/[0-9]+/ правильное заполнение https://regex101.com/r/RfmP9B/1
            (CUB[0-9]{10}\/[0-9]{4})?(;)?
        }

    });*/
