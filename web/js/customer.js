$(function (){
    initCustomerDateRange();

    $(document)
        .off('pjax:end.customerDateRange')
        .on('pjax:end.customerDateRange', initCustomerDateRange);
});

function initCustomerDateRange(){
    const input = $('.customer-register-date');

    if (!input.length || typeof input.daterangepicker !== 'function'){
        return;
    }

    input.each(function (){
        const field = $(this);

        if (field.data('daterangepicker')){
            return;
        }

        field.daterangepicker({
            autoUpdateInput: false,
            locale: {
                format: 'DD.MM.YYYY HH:mm',
                applyLabel: 'Применить',
                cancelLabel: 'Очистить',
                firstDay: 1,
                customRangeLabel: 'Пользовательский'
            },
            alwaysShowCalendars: true,
            opens: 'left'
        }).on('apply.daterangepicker', function (event, picker){
            field.val(
                picker.startDate.format('DD.MM.YYYY HH:mm')
                + ' - '
                + picker.endDate.format('DD.MM.YYYY HH:mm')
            );
            $('#customer-grid-view').yiiGridView('applyFilter');
        }).on('cancel.daterangepicker', function (){
            field.val('');
            $('#customer-grid-view').yiiGridView('applyFilter');
        });
    });
}
