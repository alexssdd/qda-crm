$(function (){
    const revealTimeout = 10000;
    const body = $('body');

    body.off('click.executorPhoneReveal', '.js-executor-phone-toggle');
    body.on('click.executorPhoneReveal', '.js-executor-phone-toggle', function (event){
        event.preventDefault();
        event.stopPropagation();

        const button = $(this);
        const container = button.closest('.executor-phone');
        const value = container.find('.executor-phone__value');

        if (button.prop('disabled')){
            return;
        }

        $('.executor-phone--revealed').not(container).each(function (){
            const hide = $(this).data('hidePhone');
            if (typeof hide === 'function'){
                hide();
            }
        });

        clearTimeout(container.data('hideTimer'));
        button.prop('disabled', true).text('...');

        $.post(button.data('url'))
            .done(function (response){
                if (!response || !response.phone){
                    button.text('Показать').prop('disabled', false);
                    return;
                }

                const hide = function (){
                    button.off('click.executorPhoneHide');
                    value.text(button.data('masked'));
                    button.text('Показать')
                        .attr('aria-label', 'Показать телефон исполнителя')
                        .prop('disabled', false);
                    container
                        .removeClass('executor-phone--revealed')
                        .removeData('hidePhone')
                        .removeData('hideTimer');
                };

                value.text(response.phone);
                button.text('Скрыть')
                    .attr('aria-label', 'Скрыть телефон исполнителя')
                    .prop('disabled', false);
                container
                    .addClass('executor-phone--revealed')
                    .data('hidePhone', hide);

                button.off('click.executorPhoneHide').one('click.executorPhoneHide', function (hideEvent){
                    hideEvent.preventDefault();
                    hideEvent.stopPropagation();
                    clearTimeout(container.data('hideTimer'));
                    hide();
                });

                container.data('hideTimer', setTimeout(hide, revealTimeout));
            })
            .fail(function (){
                button.text('Показать').prop('disabled', false);
            });
    });
});
