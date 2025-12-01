window.Cart = {
    // Variables
    cartParams: {},
    deliveryParams: {},
    timer: null,
    items: {},
    product_id: null,
    cost: 0,
    delivery_cost: 0,

    // Init
    init: function (){
        Cart.initSearch();
        Cart.initInputs();
    },
    initSearch: function (){
        let body = $('body');
        let table = $('.cart-products .modal-table tbody');
        body.on('keyup', '.cart-search__input', function (){
            clearTimeout(Cart.timer);

            Cart.timer = setTimeout(Cart.productsSearch, 1000);
        });

        table.on('click', '.modal-table__selector', function (){
            Cart.product_id = $(this).attr('data-id');
            Cart.actionsEnable();
        });
    },
    initInputs: function (){
        // Variables
        let body = $('body');

        // Handlers
        body.on('change', '#cart-merchant_id, #cart-city_id', Cart.productsSearch);
        body.on('change', '#cart-delivery_method', Cart.handleDelivery);

        // Phone
        body.on('change', '#cart-phone', function (){
            Cart.customer($(this).val());
        });
    },

    // Handlers
    handleDelivery: function (){
        let delivery = parseInt($(this).val());
        let deliveryParams = Cart.deliveryParams[delivery];
        let blockAddress = $('.cart-form__address');
        let blockStore = $('.cart-form__store');
        let inputPaymentMethod = $('#cart-payment_method');

        // Hide blocks
        blockAddress.hide();
        blockStore.hide().removeClass('required');

        // Clear address and store
        Cart.addressClear();
        Cart.storeClear();

        // Clear delivery cost
        Cart.deliveryCostSet(0);

        // Check delivery params
        if (!deliveryParams){
            return;
        }

        // Payment methods
        inputPaymentMethod.html('<option value></option>');
        for (let paymentMethod of Object.keys(deliveryParams['payment_methods'])){
            inputPaymentMethod.append('<option value="' + paymentMethod + '">' + deliveryParams['payment_methods'][paymentMethod] + '</option>');
        }

        // Additional handles
        if (deliveryParams['show_address']){
            blockAddress.css({display: 'flex'});
        }
        if (deliveryParams['show_store']){
            blockStore.css({display: 'flex'});
        }
        if (deliveryParams['required_store']){
            blockStore.addClass('required');
        }
    },

    // Products
    productsSearch: function (){
        let table = $('.cart-products .modal-table tbody');
        let loader = $('.cart-products__loader');
        let merchantId = $('#cart-merchant_id').val();
        let cityId = $('#cart-city_id').val();
        let customerId = $('#cart-customer_id').val();
        let query = $('.cart-search__input').val();

        loader.addClass('cart-products__loader--show');
        $.get(UrlManager.to('cart', 'search', {
            query: query,
            merchant_id: merchantId,
            city_id: cityId,
            customer_id: customerId
        }), function (res){
            table.html(res);
            loader.removeClass('cart-products__loader--show');
            Cart.product_id = null;
            Cart.actionsDisable();
        })
    },

    // Customer
    customer: function (phone){
        // Variables
        let inputCustomer = $('#cart-customer_id');
        let inputName = $('#cart-name');

        // Set default
        inputCustomer.val('');

        // Prepare data
        let data = {
            city_id: $('#cart-city_id').val(),
            phone: phone
        };
        data[yii.getCsrfParam()] = yii.getCsrfToken();

        // Send request
        $.post(UrlManager.to('cart', 'customer'), data, function (res){
            if (res['status'] === 'success'){
                // Set data
                inputCustomer.val(res['data']['id']);
                inputName.val(res['data']['name']);
            }
        });
    },

    // Address
    addressSelect: function(){
        let params = {
            city_id: $('#cart-city_id').val(),
            customer_id: $('#cart-customer_id').val(),
            phone: $('#cart-phone').val(),
            address: $('#cart-address').val(),
            lat: $('#cart-lat').val(),
            lng: $('#cart-lng').val(),
            house: $('#cart-house').val(),
            apartment: $('#cart-apartment').val(),
            intercom: $('#cart-intercom').val(),
            entrance: $('#cart-entrance').val(),
            floor: $('#cart-floor').val(),
            type: $('#cart-address_type').val(),
            title: $('#cart-address_title').val(),
        };

        NProgress.start();
        $.post(UrlManager.to('cart', 'address-select'), params, function (res){
            Modal.contentAdditional(res);
            Modal.open('.modal-additional');
            NProgress.done();
        });
    },
    addressSelectFinish: function (result) {
        Cart.addressSave(result);
        Cart.deliveryCalc();
    },
    addressSave: function (result){
        if (result['status'] === 'error'){
            alert(result['message']);
            return;
        }

        let data = result['data'];

        // Set values
        $('.cart-form__address-block').html(data['label'] ? data['label'] : '');
        $('#cart-address').val(data['address']).trigger('change');
        $('#cart-address_type').val(data['type']).trigger('change');
        $('#cart-lat').val(data['lat']).trigger('change');
        $('#cart-lng').val(data['lng']).trigger('change');
        $('#cart-address_title').val(data['title']).trigger('change');
        $('#cart-house').val(data['house']).trigger('change');
        $('#cart-apartment').val(data['apartment']).trigger('change');
        $('#cart-intercom').val(data['intercom']).trigger('change');
        $('#cart-entrance').val(data['entrance']).trigger('change');
        $('#cart-floor').val(data['floor']).trigger('change');
    },
    addressClear: function (){
        Cart.addressSave({
            status: 'success',
            data: {}
        });
    },
    
    // Delivery
    deliveryCalc: function (){
        let lat = $('#cart-lat').val();
        let lng = $('#cart-lng').val();

        // Calc delivery cost
        let params = {
            merchant_id: $('#cart-merchant_id').val(),
            city_id: $('#cart-city_id').val(),
            lat: lat,
            lng: lng,
            products: []
        };

        // Set products
        $('.cart-items tbody tr').each(function (){
            params.products.push({
                id: $(this).attr('data-id'),
                sku: $(this).attr('data-sku'),
                quantity: parseInt($(this).find('.cart-items__quantity').val()),
            });
        });

        // Check params
        if (!lat || !lng || !params.products.length){
            return;
        }

        // Set params
        params[yii.getCsrfParam()] = yii.getCsrfToken();

        // Send request
        Cart.loaderShow('Идет расчет стоимости доставки');
        $.post(UrlManager.to('cart', 'calc-delivery'), params, function (res){
            Cart.loaderHide();

            if (res['status'] === 'error'){
                alert(res['message']);
                return;
            }

            // Update cost
            Cart.deliveryCostSet(res['data']['cost']);

            // Set store
            $('#cart-store_id').val(res['data']['store_id']).trigger('change');
        });
    },
    deliveryCostSet: function (cost){
        // Variables
        let inputDeliveryCost = $('#cart-delivery_cost');

        // Set
        Cart.delivery_cost = cost;
        inputDeliveryCost.val(Cart.delivery_cost);

        // Total calc
        Cart.totalCalc();
    },

    // Total calc
    totalCalc: function (){
        let blockDelivery = $('.cart-total__item--delivery .cart-total__value');
        let blockTotal = $('.cart-total__item--total .cart-total__value');

        blockDelivery.text(Formatter.asDecimal(Cart.delivery_cost));
        blockTotal.text(Formatter.asDecimal(Cart.cost + Cart.delivery_cost));
    },

    // Store
    storeSelect: function(){
        // Calc delivery cost
        let params = {
            merchant_id: $('#cart-merchant_id').val(),
            city_id: $('#cart-city_id').val(),
            products: []
        };

        // Set products
        $('.cart-items tbody tr').each(function (){
            params.products.push({
                sku: $(this).attr('data-sku'),
                quantity: parseInt($(this).find('.cart-items__quantity').val()),
            });
        });

        // Check params
        if (!params.products.length){
            return;
        }

        // Set params
        params[yii.getCsrfParam()] = yii.getCsrfToken();

        // Send request
        Cart.loaderShow('Загрузка точек продаж');
        $.post(UrlManager.to('cart', 'stores'), params, function (res){
            Cart.loaderHide();
            Modal.open('.modal-additional');
            Modal.contentAdditional(res);
        });
    },
    storeSave: function (){
        let row = $('.modal-additional .modal-table__tr--selected');

        if (!row.length){
            return;
        }

        // Set store
        $('#cart-store_id').val(row.attr('data-id')).trigger('change');
        $('.cart-form__store-block').html(row.attr('data-name'));

        // Close modal
        Modal.closeAdditional();
    },
    storeClear: function (){
        let inputStore = $('#cart-store_id');
        let labelStore = $('.cart-form__store-block');

        inputStore.val('').trigger('change');
        labelStore.html('');
    },

    // Actions
    actionsDisable: function (){
        $('.cart-actions__button').prop('disabled', true);
    },
    actionsEnable: function (){
        $('.cart-actions__button').prop('disabled', false);
    },
    actionAddItem: function (){
        if (Cart.product_id === null){
            return;
        }

        Cart.itemsAdd(Cart.product_id);
    },
    actionStockOnline: function (){
        if (Cart.product_id === null){
            return;
        }

        let merchantId = $('#cart-merchant_id').val();
        let cityId = $('#cart-city_id').val();
        let url = UrlManager.to('cart', 'stock-online', {merchantId: merchantId, cityId: cityId, productId: Cart.product_id});
        Modal.openAdditional(url);
    },
    actionStockCity: function (){
        if (Cart.product_id === null){
            return;
        }

        let merchantId = $('#cart-merchant_id').val();
        let url = UrlManager.to('cart', 'stock-city', {merchantId: merchantId, productId: Cart.product_id});
        Modal.openAdditional(url);
    },
    actionDefectura: function (){
        if (Cart.product_id === null){
            return;
        }

        let url = UrlManager.to('cart', 'defectura', {
            productId: Cart.product_id,
            cityId: $('#cart-city_id').val()
        });
        Modal.openAdditional(url);
    },

    // Items
    itemsAdd: function (id){
        // Variables
        let item = Cart.itemFind(id);
        let cartBody = $('.cart-items tbody');
        let row = $('.cart-products tbody tr[data-id=' + id + ']');

        // Check exists
        if (item.length){
            Cart.itemsPlus(id);
            return;
        }

        // Add item
        let template = $('#templateCartItem').html();
        template = template.replace(/\{id}/g, id);
        template = template.replace(/\{sku}/g, row.find('.cart-products__sku').text());
        template = template.replace(/\{name}/g, row.find('.cart-products__name').text());
        template = template.replace(/\{price}/g, row.attr('data-price'));
        template = template.replace(/\{quantity}/g, 1);
        cartBody.append(template);
        cartBody.animate({
            scrollTop: 10000
        }, 500);

        // Handle items
        Cart.itemsIndex();
        Cart.itemsCalc();
    },
    itemsPlus: function (id){
        let item = Cart.itemFind(id);

        if (!item.length){
            return;
        }

        let input = item.find('.cart-items__quantity');
        let value = parseInt(input.val());

        // Plus quantity
        input.val(value + 1);

        // Handle items
        Cart.itemsCalc();
    },
    itemsChange: function (target){
        let input = $(target);
        let value = parseInt(input.val());

        if (value <= 0){
            input.val(1);
        }

        // Handle items
        Cart.itemsCalc();
    },
    itemsRemove: function (id){
        let item = Cart.itemFind(id);

        if (!item.length){
            return;
        }

        // Remove item
        item.remove();

        // Handle items
        Cart.itemsIndex();
        Cart.itemsCalc();
    },
    itemsIndex: function (){
        let index = 1;
        $('.cart-items tbody tr').each(function (){
            $(this).find('.cart-items__index').text(index);
            index++;
        });
    },
    itemsCalc: function (){
        // Variables
        let blockCost = $('.cart-total__item--cost .cart-total__value')
        let blockDelivery = $('.cart-total__item--delivery .cart-total__value')
        let blockTotal = $('.cart-total__item--total .cart-total__value')
        let data = {
            merchant_id: $('#cart-merchant_id').val(),
            city_id: $('#cart-city_id').val(),
            customer_id: $('#cart-customer_id').val(),
            products: []
        };

        // Clear store
        Cart.storeClear();

        // Collect products
        $('.cart-items tbody tr').each(function (){
            data.products.push({
                id: $(this).attr('data-id'),
                quantity: parseInt($(this).find('.cart-items__quantity').val()),
            });
        });

        if (!data.products.length){
            blockCost.text('0');
            blockDelivery.text(Formatter.asDecimal(Cart.delivery_cost));
            blockTotal.text(Formatter.asDecimal(Cart.delivery_cost));

            return;
        }

        // Set csrf
        data[yii.getCsrfParam()] = yii.getCsrfToken();

        // Send request
        $.post(UrlManager.to('cart', 'calc-products'), data, function (res){
            if (res['status'] === 'error'){
                alert(res['message']);
                return;
            }

            // Update products
            for (let product of res['data']['products']){
                let item = Cart.itemFind(product['id']);
                if (!item.length){
                    continue;
                }

                item.find('.cart-items__price').text(Formatter.asDecimal(product['price']));
            }

            // Update cost
            Cart.cost = res['data']['cost'];
            blockCost.text(Formatter.asDecimal(Cart.cost));
            blockDelivery.text(Formatter.asDecimal(Cart.delivery_cost));
            blockTotal.text(Formatter.asDecimal(Cart.cost + Cart.delivery_cost));
        });

        // Delivery calc
        Cart.deliveryCalc();
    },
    itemFind: function (id){
        return $('.cart-items tbody tr[data-id=' + id + ']');
    },

    // Methods
    loaderShow: function (text) {
        $('.cart-loader').addClass('is-visible');
        $('.cart-loader__text').text(text);
    },
    loaderHide: function () {
        $('.cart-loader').removeClass('is-visible');
    },
}