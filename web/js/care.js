$(function (){
    Care.init();
});

window.Care = {
    // Variables
    id: null,
    isPositive: false,
    valueComplaintReason: null,
    valueSolutionMeasures: null,

    // Init methods
    init: function (){
        Care.initInputs();
        Care.initTable();
        Care.initQueryParams();
    },
    initModel: function (){
        let body = $('body');

        // Category
        body.on('change', '#care-category', Care.handleCategory);

        // Handlers
        Care.handleCategory(true);
    },
    initInputs: function (){
        let body = $('body');

        // Date picker
        $('.care-filter__date').daterangepicker({
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
            Care.refresh();
        }).on('cancel.daterangepicker', function () {
            $(this).val('');
            Care.refresh();
        });

        // My
        $('#filter-my').on('change', function (e) {
            let checked = $(this).prop('checked');
            $('#my').val(~~checked);

            $('#care-grid-view').yiiGridView('applyFilter');
        });

        // Chat input
        body.on('keypress', '.care-history__input', function (e){
            if (e.code === 'Enter'){
                Care.chatSend();
            }
        });
    },
    initTable: function (){
        $('body').on('click', '.care-table tbody > tr:not(.empty-row)', function () {
            let row = $(this);
            let id = row.data('key');

            row.siblings().removeClass('care-table__tr--active');
            row.addClass('care-table__tr--active');
            $('.care-filter__id').val(id);
            Care.refresh();
        });
    },
    initQueryParams: function (){
        let params = new URLSearchParams(location.search);
        let id = params.get('lead_id');
        if (!id){
            return;
        }

        Modal.openUrl(UrlManager.to('appeal', 'index', {lead_id: id}));
    },
    initTime: function (seconds, stop){
        let block = $('.care-header__time');
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
    refresh: function (){
        $('.care-grid').yiiGridView('applyFilter');
    },
    actions: function (){
        $('.care-actions').slideToggle(250);
    },
    filter: function (){
        Modal.open('.modal-filter');
    },
    filterSubmit: function () {
        $('.modal-filter *[data-input]').each(function () {
            let id = $(this).data('input');
            $('#' + id).val($(this).val());
        });
        Modal.close();
        $('#care-grid-view').yiiGridView('applyFilter');
        Care.filterButtonChange();
    },
    filterButtonChange: function () {
        let active = false;
        let button = $('.care-filter__button--filter');

        $('.modal-filter *[data-input]').each(function () {
            if ($(this).val() !== ''){
                active = true;
            }
        });

        button.removeClass('care-filter__button--active');
        if (active){
            button.addClass('care-filter__button--active');
        }
    },
    handleCategory: function (isInit = false){
        let valueCategory = $('#care-category').val();
        let inputComplaintReason = $('#care-complaint_reason');
        let inputSolutionMeasures = $('#care-solution_measures');
        let inputStoreNumber = $('#care-store_number');
        let fieldStoreNumber = $('.care-body__item--store_number');
        let fieldDeliveryLate = $('.care-body__item--delivery_late');

        // Set default
        fieldStoreNumber.val('').hide().removeClass('required');
        fieldDeliveryLate.val('').hide();
        inputComplaintReason.html('<option value>' + Messages['Select value'] + '</option>');
        inputSolutionMeasures.html('<option value>' + Messages['Select value'] + '</option>');
        inputStoreNumber.prop('required', false);

        // Delivery
        if (valueCategory === CareHelper['category_delivery'] && !Care.isPositive){
            fieldDeliveryLate.css({'display': 'flex'});
        }

        // Store
        if (valueCategory === CareHelper['category_store']){
            fieldStoreNumber.css({'display': 'flex'}).addClass('required');
            inputStoreNumber.prop('required', true);
        }

        // Complaint reason
        let complaintReasons = CareHelper['complaint_reasons'][valueCategory];
        if (Array.isArray(complaintReasons)){
            for (let reason of complaintReasons){
                inputComplaintReason.append('<option value="' + reason + '">' + reason + '</option>');
            }
        }

        // Solution measures
        let solutionMeasures = CareHelper['solution_measures_negative'][valueCategory];
        if (Care.isPositive){
            solutionMeasures = CareHelper['solution_measures_positive'][valueCategory];
        }
        if (Array.isArray(solutionMeasures)){
            for (let solutionMeasure of solutionMeasures){
                inputSolutionMeasures.append('<option value="' + solutionMeasure + '">' + solutionMeasure + '</option>');
            }
        }

        if (isInit){
            inputComplaintReason.val(Care.valueComplaintReason);
            inputSolutionMeasures.val(Care.valueSolutionMeasures);
        }
    },

    // Chat
    chatSend: function (){
        // Variables
        let input = $('.care-history__input');
        let value = input.val();

        // Check value
        if (!value.length){
            return;
        }

        let params = {
            message: value
        };
        params[yii.getCsrfParam()] = yii.getCsrfToken();
        $.post(UrlManager.to('care', 'chat-message', {id: Care.id}), params, function (res){
            if (res['status'] === 'error'){
                alert(res['message']);
                return;
            }

            // Clear input
            input.val('');

            // Reload chat
            $('.care-chat').html(res['data']);
        });
    },

    // SMS
    smsToggle: function (){
        $('.care-call').hide();
        $('.care-sms').toggle();
    },
    smsSend: function (){
        // todo
    },

    // Call
    callToggle: function (){
        $('.care-sms').hide();
        $('.care-call').toggle();
    },
}