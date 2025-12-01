$(function (){
    Lead.init();
});

window.Lead = {
    // Variables
    id: null,
    modal: $('.lead-modal'),
    modalBack: $('.lead-back'),

    // Init methods
    init: function (){
        Lead.initInputs();
        Lead.initQueryParams();
    },
    initInputs: function (){
        let body = $('body');

        // Date picker
        $('.lead-filter__date').daterangepicker({
            autoUpdateInput: false,
            ranges: {
                'Сегодня': [moment(), moment()],
                'Вчера': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'За 7 Дней': [moment().subtract(6, 'days'), moment()],
                'За 30 Дней': [moment().subtract(29, 'days'), moment()],
                'Этот Месяц': [moment().startOf('month'), moment().endOf('month')],
                'Посл Месяц': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            },
            locale: {
                format: 'DD.MM.YYYY HH:mm',
                applyLabel: 'Применить',
                cancelLabel: 'Очистить',
                firstDay: 1,
                customRangeLabel: 'Пользовательский',
                monthNames: [
                    'Январь',
                    'Февраль',
                    'Март',
                    'Апрель',
                    'Май',
                    'Июнь',
                    'Июль',
                    'Август',
                    'Сентябрь',
                    'Октябрь',
                    'Ноябрь',
                    'Декабрь'
                ],
            },
            alwaysShowCalendars: true,
            opens: 'left'
        }).on('apply.daterangepicker', function (ev, picker) {
            $(this).val(picker.startDate.format('DD.MM.YYYY HH:mm') + ' - ' + picker.endDate.format('DD.MM.YYYY HH:mm'));
            Lead.refresh();
        }).on('cancel.daterangepicker', function () {
            $(this).val('');
            Lead.refresh();
        });

        // Channel
        body.on('change', '.lead-filter__city, .lead-filter__brand, .lead-filter__channel', function (){
            Lead.refresh();
        });
    },
    initQueryParams: function (){
        let params = new URLSearchParams(location.search);
        let id = params.get('id');
        if (!id){
            return;
        }

        Lead.open(id);
    },
    initTime: function (seconds, stop){
        let block = $('.lead-header__time');
        block.timeTo({
            countdown: false,
            seconds: seconds,
            displayDays: 2,
            fontFamily: 'tahoma, arial, verdana, sans-serif'
        });
        if (stop){
            block.timeTo('stop');
        }
    },

    // Methods
    open: function (id){
        $.get(UrlManager.to('lead', 'detail', {id: id}), function (res){
            Lead.modal.html(res);
            Lead.modalOpen();
            Lead.id = id;
        });
    },
    refresh: function (){
        $('#lead-form').yiiActiveForm('submitForm');
    },
    filter: function (){
        Modal.open('.modal-filter');
    },
    actions: function (){
        $('.lead-actions').slideToggle(250);
    },
    modalOpen: function (){
        Lead.modal.addClass('lead-modal--active');
        Lead.modalBack.addClass('lead-back--active');
    },
    modalClose: function (){
        Lead.modal.removeClass('lead-modal--active');
        Lead.modalBack.removeClass('lead-back--active');
    },

    // Chat
    chatSend: function (){
        // Variables
        let input = $('.lead-history__input');
        let value = input.val();

        // Check value
        if (!value.length){
            return;
        }

        // Confirm
        if (!confirm('Вы уверены что хотите отправить это сообщение?')){
            return;
        }

        let params = {
            message: value
        };
        params[yii.getCsrfParam()] = yii.getCsrfToken();
        $.post(UrlManager.to('lead', 'chat-message', {id: Lead.id}), params, function (res){
            if (res['status'] === 'error'){
                alert(res['message']);
                return;
            }

            // Clear input
            input.val('');

            // Reload chat
            $('.lead-chat').html(res['data']);
        });
    },

    // SMS
    smsToggle: function (){
        $('.lead-call').hide();
        $('.lead-sms').toggle();
    },
    smsSend: function (){
        // todo
    },

    // Call
    callToggle: function (){
        $('.lead-sms').hide();
        $('.lead-call').toggle();
    },
}