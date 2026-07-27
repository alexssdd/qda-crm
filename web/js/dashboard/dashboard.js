/* Document ready
----------------------------------------*/
$(function () {
    ChannelExport.init();

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
    init: function (){
        let body = $('body');

        body.off('click.channelExportCity', '.js-channel-export-city');
        body.on('click.channelExportCity', '.js-channel-export-city', function (){
            ChannelExport.detailCity($(this).attr('data-city-id'));
        });

        body.off('click.channelExportStore', '.js-channel-export-store');
        body.on('click.channelExportStore', '.js-channel-export-store', function (){
            ChannelExport.detailStore(
                $(this).attr('data-city-id'),
                $(this).attr('data-store-id')
            );
        });
    },
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
        let template = $($('#templateDetail').html());
        template.find('.js-channel-export-name').text(data['name'] || '');
        modal.empty().append(template);

        // Prepare rows
        window.dias = data['all'];
        for (const [key, channel] of Object.entries(data['all'])){
            let templateRow = $($('#templateDetailRow').html());
            templateRow.find('.js-channel-export-name').text(channel['name'] || '');
            templateRow.find('.js-channel-export-value').text(channel['export_label'] || '');
            templateRow.find('.js-channel-stock-value').text(channel['stock_label'] || '');
            modal.find('tbody').append(templateRow);
        }

        // Modal
        Modal.open('.modal-main');
    }
}
