$(function (){
    // Variables
    let body = $('body');

    // Init
    Order.init();

    // Product context
    body.on('contextmenu', function () {
        $('.product-context').hide();
    });
    body.on('click', function () {
        $('.product-context').hide();
    });

    // Assembly all
    body.on('submit', '#order-assembly-all', function (){
        let row = $(this).find('.modal-table__tr--selected');

        if (!row.length){
            alert('Необходимо выбрать точку продажи');
            return false;
        }

        // Set store
        $('#order-assembly-store_id').val(row.data('id'));

        return true;
    })
});

window.Order = {
    // Variables
    id: null,
    product_id: null,
    productSearchTimer: null,
    productSearchFlag: false,
    cancelReasons: [],

    // Init methods
    init: function (){
        Order.initInputs();
        Order.initTable();
        Order.initQueryParams();
        Order.initProductSearch();
    },
    initInputs: function (){
        let body = $('body');

        // Date picker
        $('.order-filter__date').daterangepicker({
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
            Order.refresh();
        }).on('cancel.daterangepicker', function () {
            $(this).val('');
            Order.refresh();
        });

        // My
        $('#filter-my').on('change', function (e) {
            let checked = $(this).prop('checked');
            $('#my').val(~~checked);

            $('#order-grid-view').yiiGridView('applyFilter');
        });

        // Chat input
        body.on('keypress', '.order-history__input', function (e){
            if (e.code === 'Enter'){
                Order.chatSend();
            }
        });
    },
    initTable: function (){
        $('body').on('click', '.order-table tbody > tr:not(.empty-row)', function () {
            let row = $(this);
            let id = row.data('key');

            row.siblings().removeClass('order-table__tr--active');
            row.addClass('order-table__tr--active');
            $('.order-filter__id').val(id);
            Order.refresh();
        });
    },
    initQueryParams: function (){
        let params = new URLSearchParams(location.search);
        let id = params.get('lead_id');
        if (!id){
            return;
        }

        Modal.openUrl(UrlManager.to('cart', 'index', {lead_id: id}));
    },
    initProductSearch: function (){
        let body = $('body');
        body.on('keyup', '.order-add-product__input', function (){
            clearTimeout(Order.productSearchTimer);

            Order.productSearchTimer = setTimeout(Order.productsSearch, 1000);
        });

        // Clear
        Order.product_id = null;

        // Selector
        body.on('click', '.order-add-product .modal-table__selector', function (){
            Order.product_id = $(this).attr('data-id');
            $('.order-add-product__submit').prop('disabled', false);
        });

        // Form
        body.on('submit', '.order-add-product__form', function (){
            // Check flag
            if (Order.productSearchFlag){
                return false;
            }

            // Set flag
            Order.productSearchFlag = true;

            // Check product id
            if (!Order.product_id){
                alert('Необходимо выбрать товар');
                Order.productSearchFlag = false;
                return false;
            }

            // Check quantity
            let quantity = parseInt($('.order-add-product .modal-table__selector[data-id=' + Order.product_id + '] .modal-table__input').val());
            if (quantity <= 0){
                alert('Необходимо указать минимум 1 шт');
                Order.productSearchFlag = false;
                return false;
            }

            // Set input values
            $('.order-add-product__product-id').val(Order.product_id);
            $('.order-add-product__quantity').val(quantity);

            Order.productSearchFlag = false;

            return true;
        });
    },
    initTime: function (seconds, stop){
        let block = $('.order-header__time');
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
    initCancel: function (){
        // Variables
        let body = $('body');

        body.on('change', '#ordercancel-reason', function (){
            let fieldReasonAdditional = $('.order-cancel-reason__additional');
            let inputReasonAdditional = fieldReasonAdditional.find('select');

            // Hide
            fieldReasonAdditional.hide();
            inputReasonAdditional.html('');

            // Additional reasons
            let additionalReasons = Order.cancelReasons[$(this).val()]['children'];
            if (Array.isArray(additionalReasons)){
                // Prompt
                inputReasonAdditional.append('<option value>' + Messages['Select value'] + '</option>');

                // Options
                for (let additionalReason of additionalReasons){
                    inputReasonAdditional.append('<option value="' + additionalReason + '">' + additionalReason + '</option>');
                }

                // Show
                fieldReasonAdditional.show();
            }
        });
    },

    // Products
    productsSearch: function (){
        let loader = $('.order-add-product__loader');
        let query = $('.order-add-product__input').val();

        loader.addClass('order-add-product__loader--show');
        $.get(UrlManager.to('order', 'product-search', {
            id: Order.id,
            query: query
        }), function (res){
            $('.order-add-product .modal-table tbody').html(res);
            loader.removeClass('order-add-product__loader--show');
            Order.product_id = null;
            $('.order-add-product__submit').prop('disabled', true);
        })
    },

    // Methods
    refresh: function (){
        $('.order-grid').yiiGridView('applyFilter');
    },
    create: function (){
        Modal.openUrl(UrlManager.to('cart', 'index'));
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
        $('#order-grid-view').yiiGridView('applyFilter');
        Order.filterButtonChange();
    },
    filterButtonChange: function () {
        let active = false;
        let button = $('.order-filter__button--filter');

        $('.modal-filter *[data-input]').each(function () {
            if ($(this).val() !== ''){
                active = true;
            }
        });

        button.removeClass('order-filter__button--active');
        if (active){
            button.addClass('order-filter__button--active');
        }
    },
    actions: function (){
        $('.order-actions').slideToggle(250);
    },

    // Assembly
    addAssemblyStock: function(){
        let orderProductId = Order.getContextProductId();
        let url = UrlManager.to('order', 'add-assembly-stock', {orderProductId: orderProductId});

        Modal.openUrl(url);
    },
    addAssemblyManual: function(){
        let orderProductId = Order.getContextProductId();
        let url = UrlManager.to('order', 'add-assembly-manual', {orderProductId: orderProductId});

        Modal.openUrl(url);
    },
    addAssemblyAll: function(){
        let orderProductId = Order.getContextProductId();
        let url = UrlManager.to('order', 'add-assembly-all', {orderProductId: orderProductId});

        Modal.openUrl(url);
    },
    removeAssembly: function(){
        let orderProductId = Order.getContextProductId();
        let url = UrlManager.to('order', 'remove-assembly', {orderProductId: orderProductId});

        let params = {};
        params[yii.getCsrfParam()] = yii.getCsrfToken();
        $.post(url, params, function (res){
            if (res['status'] === 'error'){
                alert(res['message']);
                return;
            }

            $('.order-products tr[data-id=' + orderProductId + '] .order-products__assemblies').html('');
            alert('Сборка успешно удалена');
        });
    },
    removeAssemblyAll: function(){
        let url = UrlManager.to('order', 'remove-assembly-all', {id: Order.id});

        let params = {};
        params[yii.getCsrfParam()] = yii.getCsrfToken();
        $.post(url, params, function (res){
            if (res['status'] === 'error'){
                alert(res['message']);
                return;
            }

            $('.order-products .order-products__assemblies').each(function(){
                $(this).html('');
            })

            alert('Сборка успешно удалена');
        });
    },

    // Product
    addProduct: function(){
        let url = UrlManager.to('order', 'add-product', {id: Order.id});

        Modal.openUrl(url);
    },
    updateProducts: function(){
        let url = UrlManager.to('order', 'update-products', {id: Order.id});

        Modal.openUrl(url);
    },
    contextProduct: function (event, orderId, productId){
        let menu = $('.product-context');
        event = event || window.event;
        event.cancelBubble = true;

        // Copy button
        let selectionText = window.getSelection().toString();
        let copyButton = $('.product-context__link--copy');
        if (selectionText.length){
            copyButton.show();
        } else {
            copyButton.hide();
        }

        // Задаём позицию контекстному меню
        menu.css({
            top: Order.contextPosition(event).y + "px",
            left: Order.contextPosition(event).x + "px"
        });
        menu.attr('order-id', orderId);
        menu.attr('product-id', productId);
        menu.show();
        
        return false;
    },
    contextPosition: function (event) {
        let x = y = 0;
        let d = document;
        let w = window;

        if (d.attachEvent != null) { // Internet Explorer & Opera
            x = w.event.clientX + (d.documentElement.scrollLeft ? d.documentElement.scrollLeft : d.body.scrollLeft);
            y = w.event.clientY + (d.documentElement.scrollTop ? d.documentElement.scrollTop : d.body.scrollTop);
        } else if (!d.attachEvent && d.addEventListener) { // Gecko
            x = event.clientX + w.scrollX;
            y = event.clientY + w.scrollY;
        }

        return {x:x, y:y};
    },
    getContextProductId: function (){
        return $('.product-context').attr('product-id');
    },

    // Chat
    chatSend: function (){
        // Variables
        let input = $('.order-history__input');
        let value = input.val();

        // Check value
        if (!value.length){
            return;
        }

        let params = {
            message: value
        };
        params[yii.getCsrfParam()] = yii.getCsrfToken();
        $.post(UrlManager.to('order', 'chat-message', {id: Order.id}), params, function (res){
            if (res['status'] === 'error'){
                alert(res['message']);
                return;
            }

            // Variables
            let chat = $('.order-chat');

            // Clear input
            input.val('');

            // Reload chat
            chat.html(res['data']);

            // Scroll
            $('.order-history__body').animate({
                scrollTop: chat.innerHeight()
            });

            // Animate last event
            $('.order-chat__event:last-child').addClass('animate__animated animate__fadeIn animate__slow')
        });
    },

    // Whatsapp
    whatsappToggle: function (){
        $('.order-call').hide();
        $('.order-sms').hide();
        $('.order-whatsapp').toggle();
    },
    whatsappSend: function (){
        if (!confirm('Вы уверены что хотите отправить это сообщение?')){
            return;
        }

        let templateInput = $('.order-whatsapp__template')
        let templateValue = templateInput.val();

        // Check value
        if (!templateValue.length){
            return;
        }

        let params = {
            template: templateValue
        };
        params[yii.getCsrfParam()] = yii.getCsrfToken();
        $.post(UrlManager.to('order', 'whatsapp', {id: Order.id}), params, function (res){
            // Toggle
            Order.whatsappToggle();

            if (res['status'] === 'error'){
                alert(res['message']);
                return;
            }

            // Variables
            let chat = $('.order-chat');

            // Clear input
            templateInput.val('');

            // Reload chat
            chat.html(res['data']);

            // Scroll
            $('.order-history__body').animate({
                scrollTop: chat.innerHeight()
            });

            // Animate last event
            $('.order-chat__event:last-child').addClass('animate__animated animate__fadeIn animate__slow');
        });
    },

    // SMS
    smsToggle: function (){
        $('.order-call').hide();
        $('.order-whatsapp').hide();
        $('.order-sms').toggle();
    },
    smsSend: function (){
        // todo
    },

    // Call
    callToggle: function (){
        $('.order-sms').hide();
        $('.order-whatsapp').hide();
        $('.order-call').toggle();
    },
}

window.OrderCourier = {
    // Variables
    city_lat: null,
    city_lng: null,
    courier: null,
    customer: null,
    stores: [],

    // Map
    map: null,
    markerCourier: null,
    interval: null,
    collection: null,
    
    // Init
    init: function (params){
        OrderCourier.city_lat = params['city_lat'];
        OrderCourier.city_lng = params['city_lng'];
        OrderCourier.courier = params['courier'];
        OrderCourier.customer = params['customer'];
        OrderCourier.stores = params['stores'];

        // Init map
        ymaps.ready(OrderCourier.initMap);
    },
    initMap: function (){
        // Map
        OrderCourier.map = new ymaps.Map('orderCourierMap', {
            center: [OrderCourier.city_lat, OrderCourier.city_lng],
            zoom: 12,
            controls: ['zoomControl', 'fullscreenControl']
        }, {
            suppressMapOpenBlock: true
        });

        OrderCourier.collection = new ymaps.GeoObjectCollection();

        // Customer
        if (OrderCourier.customer){
            let customerMarker = new ymaps.Placemark([OrderCourier.customer['lat'], OrderCourier.customer['lng']],
                {
                    balloonContent: OrderCourier.customer['balloon']
                },
                {
                    iconLayout: 'default#image',
                    iconImageHref: '/images/marker_user.png',
                    iconImageSize: [32, 32]
                }
            );

            // Set markers
            OrderCourier.collection.add(customerMarker);
        }

        // Stores
        for (let store of OrderCourier.stores){
            let storeMarker = new ymaps.Placemark([store['lat'], store['lng']],
                {
                    balloonContent: store['balloon']
                },
                {
                    iconLayout: 'default#image',
                    iconImageHref: '/images/marker_home.png',
                    iconImageSize: [32, 32]
                }
            );

            // Set markers
            OrderCourier.collection.add(storeMarker);
        }

        // Add collection
        OrderCourier.map.geoObjects.add(OrderCourier.collection);

        // Auto center
        OrderCourier.map.setBounds(OrderCourier.collection.getBounds());

        OrderCourier.interval = setInterval(function (){
            OrderCourier.loadCourierCoordinates();
        }, 5000);
    },
    close: function (){
        clearInterval(OrderCourier.interval);
        Modal.close();
    },
    loadCourierCoordinates: function (){
        $.post(UrlManager.to('order', 'courier-location', {id: Order.id}), function (res){
            if (res['status'] === 'error'){
                alert(res['message']);

                clearInterval(OrderCourier.interval);

                return;
            }

            OrderCourier.renderCourierCoordinates(res['data']);
        });
    },
    renderCourierCoordinates: function (data){
        if (OrderCourier.markerCourier){
            OrderCourier.markerCourier.geometry.setCoordinates([data['lat'], data['lng']]);
            OrderCourier.markerCourier.properties.set('balloonContent', data['balloon']);
        } else {
            OrderCourier.markerCourier = new ymaps.Placemark([data['lat'], data['lng']],
                {
                    balloonContent: data['balloon']
                },
                {
                    iconLayout: 'default#image',
                    iconImageHref: '/images/marker_car.png',
                    iconImageSize: [32, 32]
                }
            );

            // Set markers
            OrderCourier.collection.add(OrderCourier.markerCourier);
        }

        // Auto center
        OrderCourier.map.setBounds(OrderCourier.collection.getBounds());
    }
}