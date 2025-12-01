/* Document ready
----------------------------------------*/
$(function () {
    // Submit form
    let submitForms = [
        'form.page-filter'
    ];

    // Sale
    $('body').on('change', submitForms.join(', '), function (e) {
        $(this).submit();
    });

    // Date range
    let dateRangeInputs = [
        '#date_range',
    ];
    $(dateRangeInputs.join(', ')).daterangepicker({
        autoUpdateInput: false,
        ranges: {
            'Сегодня': [moment(), moment()],
            'Вчера': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'За 7 Дней': [moment().subtract(6, 'days'), moment()],
            'За 30 Дней': [moment().subtract(29, 'days'), moment()],
            'Этот Месяц': [moment().startOf('month'), moment().endOf('month')],
            'Посл Месяц': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
        },
        "locale": {
            "format": "DD.MM.YYYY HH:mm",
            "applyLabel": "Применить",
            "cancelLabel": "Очистить",
            "firstDay": 1,
            "monthNames": [
                "Январь",
                "Февраль",
                "Март",
                "Апрель",
                "Май",
                "Июнь",
                "Июль",
                "Август",
                "Сентябрь",
                "Октябрь",
                "Ноябрь",
                "Декабрь"
            ],
        },
        "alwaysShowCalendars": true,
        "opens": "left"
    });
    // Filter date range apply
    $(dateRangeInputs.join(', ')).on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('DD.MM.YYYY HH:mm') + ' - ' + picker.endDate.format('DD.MM.YYYY HH:mm'));
        $(this).change();
    });

    // Filter date range clear
    $(dateRangeInputs.join(', ')).on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
        $(this).change();
    });
});

// Channel export
window.ChannelExport = {
    data: {},

    // Methods
    detailCity: function (id){
        let data = ChannelExport.data[id];
        
        // Detail open
        ChannelExport.detailOpen(data);
    },
    detailStore: function (cityId, id){
        let data = ChannelExport.data[cityId]['stores'][id];
        
        // Detail open
        ChannelExport.detailOpen(data);
    },
    detailOpen: function (data){
        let modal = $('.modal-main');

        // Prepare modal
        let template = $('#templateDetail').html();
        template = template.replace(/\{name}/g, data['name']);
        modal.html(template);

        // Prepare rows
        window.dias = data['all'];
        for (const [key, channel] of Object.entries(data['all'])){
            let templateRow = $('#templateDetailRow').html();
            templateRow = templateRow.replace(/\{name}/g, channel['name']);
            templateRow = templateRow.replace(/\{export}/g, channel['export_label']);
            templateRow = templateRow.replace(/\{stock}/g, channel['stock_label']);
            modal.find('tbody').append(templateRow);
        }

        // Modal
        Modal.open('.modal-main');
    }
}