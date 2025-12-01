/* Document ready
----------------------------------------*/
$(function () {
    // Variables
    let body = $('body');

    // Moment
    moment.locale('ru');

    // Pjax
    $(document).on('pjax:start', function() { NProgress.start(); });
    $(document).on('pjax:end',   function() { NProgress.done();  });

    // Empty link
    $(document).on('click', 'a[href="#"]', function (e) {
        return false;
    });

    // Window click
    $(document).click(function (e) {
        if (e.target.closest('.header-user') == null){
            Header.userBlockHide();
        }
        if (e.target.closest('.header-menu__item--toggle') == null){
            $('.header-menu__item--toggle').removeClass('header-menu__item--toggle');
        }
    });

    // Window keyup
    $(document).keyup(function(event){
        if (event.which === 27){ // Escape
            Modal.closeAdditional();
        }
    });

    // Header menu
    $('.header-menu__link').click(function (){
        let sub = $(this).next('.header-menu__sub');
        if (!sub.length){
            return true;
        }

        // Hide current
        $('.header-menu__item--toggle').removeClass('header-menu__item--toggle');

        // Add class
        $(this).parent().addClass('header-menu__item--toggle');

        return false;
    });

    // Modal view
    body.on('click', '.js-view-modal', function () {
        Modal.openUrl($(this).attr('href'));

        return false;
    });
    body.on('click', '.js-view-additional', function () {
        Modal.openAdditional($(this).attr('href'));

        return false;
    });
    body.on('click', '.modal__close', function () {
        if ($(this).closest('.modal-additional').length){
            Modal.closeAdditional();
        } else {
            Modal.close();
        }
    });

    // Alert
    $('.alert__close').on('click', function () {
        $('.alert').remove();
    });

    // Tabs
    body.on('click', '*[data-tabs-target]', function () {
        let parent = $(this).parents('.tabs');

        // Remove classes
        parent.find('.tabs__title--active').removeClass('tabs__title--active');
        parent.find('.tabs__block--active').removeClass('tabs__block--active');

        // Add classes
        $(this).addClass('tabs__title--active');
        parent.find($(this).attr('data-tabs-target')).addClass('tabs__block--active');
    });

    // Modal table
    body.on('click', '.modal-table__selector', function (){
        let table = $(this).parents('.modal-table');
        let row = $(this).prop('tagName') === 'TR' ? $(this) : $(this).parents('tr');
        table.find('tr.modal-table__tr--selected').removeClass('modal-table__tr--selected');
        row.addClass('modal-table__tr--selected');
    });

    // Advert
    Advert.open();
});

// Formatter
window.Formatter = {
    decimal: new Intl.NumberFormat('ru', {
        style: 'decimal'
    }),
    asDecimal: function (value){
        if (typeof value !== 'number'){
            return value;
        }
        return Formatter.decimal.format(value);
    }
}

// Url manager
window.UrlManager = {
    to: function (controller, action, params) {
        let result;

        result = '/' + controller + '/' + action;

        if (params !== undefined){
            let paramsArray = [];
            $.each(params, function (key, value) {
                paramsArray.push(key + '=' + value);
            });
            if (paramsArray.length > 0){
                result = result + '?' + paramsArray.join('&');
            }
        }

        return result;
    }
};

/* Header
----------------------------------------*/
window.Header = {
    userBlock: $('.header-user'),
    userBlockToggle: function () {
        Header.userBlock.toggleClass('header-user--active');
    },
    userBlockHide: function () {
        Header.userBlock.removeClass('header-user--active');
    },
    userState: function (state){
        let stateBlock = $('.header-state');
        let stateButton = $('.header-state__button');

        // Confirm
        if (state === 'online' && !confirm('Вы уверены что хотите открыть смену?')) {
            return;
        }
        if (state === 'offline' && !confirm('Вы уверены что хотите закрыть смену?')) {
            return;
        }

        // Send
        $.post(UrlManager.to('site', 'state-' + state), function (){
            if (state === 'online'){
                stateBlock.removeClass('header-state--offline').addClass('header-state--online');
            } else {
                stateBlock.removeClass('header-state--online').addClass('header-state--offline');
            }

            stateButton.text(stateBlock.data(state))
        });
    }
}

/* Password generator
----------------------------------------*/
window.PasswordGenerator = {
    generate: function (fields) {
        let result = [];
        let characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let charactersLength = characters.length;
        for (let i = 0; i < 10; i++ ) {
            result.push(characters.charAt(Math.floor(Math.random() * charactersLength)));
        }
        let password = result.join('');

        $(fields.join(',')).val(password);
    }
}

/* Modal
----------------------------------------*/
window.Modal = {
    open: function (elem) {
        $(elem).addClass('is-visible');
        $('body').addClass('modal__open');
    },
    openUrl: function (url){
        Modal.open('.modal-main');

        NProgress.start();
        $.get(url, function (res) {
            Modal.content(res);
            NProgress.done();
        });
    },
    close: function () {
        $('.modal:not(.modal-additional).is-visible').removeClass('is-visible');
        $('body').removeClass('modal__open');
        $('.modal-main').html('');
    },
    content: function (data) {
        $('.modal-main').html(data);
    },

    // Additional
    openAdditional: function (url){
        Modal.open('.modal-additional');

        NProgress.start();
        $.get(url, function (res) {
            Modal.contentAdditional(res);
            NProgress.done();
        });
    },
    contentAdditional: function (data) {
        $('.modal-additional').html(data);
    },
    closeAdditional: function () {
        $('.modal-additional.is-visible').removeClass('is-visible');
        $('.modal-additional').html('');
    },

    // Ajax
    ajaxForm: function (elem, callback){
        let body = $('body');

        body.off('submit', elem);
        body.on('submit', elem, function (){
            let form = $(this);
            let flag = false;
            let button = form.find('button[type=submit]');

            if (flag){
                return false;
            }

            flag = true;
            button.prop('disabled', true);

            NProgress.start();
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serializeArray()
            }).done(function (res) {
                callback(res);

                flag = false;
                button.prop('disabled', false);
                Modal.closeAdditional();
                NProgress.done();
            });

            return false;
        });
    }
};

/* Auto logout
----------------------------------------*/
window.AutoLogout = {
    interval: null,
    time: null,
    seconds: 15 * 60, // 15 minutes
    run: function (){
        $(window).on('mousemove', () => {
            this.time = Math.floor(Date.now() / 1000);
        });

        AutoLogout.interval = setInterval(() => {
            if (this.time === null){
                return;
            }

            let now = Math.floor(Date.now() / 1000);

            if ((now - this.time) >= this.seconds){
                clearInterval(this.interval);
                $.post('/logout');
            }
        }, 5000);
    }
}

/* Address select
----------------------------------------*/
window.AddressSelect = {
    // Variables
    cityLat: null,
    cityLng: null,
    map: null,
    mapMarker: null,
    mapSuggest: null,
    cityId: null,
    cityName: null,
    type: null,
    doneCallback: null,
    customerAddresses: null,

    addressInput: null,
    addressValue: null,
    addressInputs: null,

    lngInput: null,
    lngValue: null,

    latInput: null,
    latValue: null,

    addressTitleInput: null,
    addressTitleValue: null,

    houseInput: null,
    apartmentInput: null,
    intercomInput: null,
    entranceInput: null,
    floorInput: null,

    // Events
    init: function (params) {
        if (window.ymaps === undefined){
            setTimeout(function () {
                AddressSelect.init(params);
            }, 1000);
            return null;
        }

        // Clear variables
        AddressSelect.map = null;
        AddressSelect.mapMarker = null;

        // Set variables
        AddressSelect.cityLat = params.cityLat;
        AddressSelect.cityLng = params.cityLng;
        AddressSelect.cityId = params.cityId;
        AddressSelect.cityName = params.cityName;
        AddressSelect.type = params.type;
        AddressSelect.doneCallback = params.doneCallback;

        // Set fields
        AddressSelect.customerAddresses = $('.customer-addresses');
        AddressSelect.addressInput = $('#address-address');
        AddressSelect.latInput = $('#address-lat');
        AddressSelect.lngInput = $('#address-lng');
        AddressSelect.addressTitleInput = $('#address-title');
        AddressSelect.houseInput = $('#address-house');
        AddressSelect.apartmentInput = $('#address-apartment');
        AddressSelect.intercomInput = $('#address-intercom');
        AddressSelect.entranceInput = $('#address-entrance');
        AddressSelect.floorInput = $('#address-floor');
        AddressSelect.addressInputs = $('.address-select__inputs');

        // Show map
        ymaps.ready(AddressSelect.initMap(params));
        AddressSelect.initEvents();
        AddressSelect.toggleFields();
    },

    // Methods
    initEvents: function(){
        $('.tab-checkbox__input').change(function () {
            AddressSelect.type = $(this).val();
            AddressSelect.toggleFields();
        });
        $('input[name="order-address-select-list"]').change(function () {
            AddressSelect.clearAdditionalFields();

            AddressSelect.addressValue = $(this).attr('data-address');
            AddressSelect.lngValue = $(this).attr('data-lng');
            AddressSelect.latValue = $(this).attr('data-lat');
            AddressSelect.addressTitleValue = $(this).attr('data-title');
            AddressSelect.setModalFormInfo();

            AddressSelect.houseInput.val($(this).attr('data-house'));
            AddressSelect.apartmentInput.val($(this).attr('data-apartment'));
            AddressSelect.intercomInput.val($(this).attr('data-intercom'));
            AddressSelect.entranceInput.val($(this).attr('data-entrance'));
            AddressSelect.floorInput.val($(this).attr('data-floor'));

            AddressSelect.setMarkerCoords(AddressSelect.latValue, AddressSelect.lngValue);
            AddressSelect.map.setCenter([AddressSelect.latValue, AddressSelect.lngValue]);
        });

        $('#addressSelectForm').on('beforeSubmit', function(){
            // Disable button
            $('.address-select__button').prop('disabled', true);

            let data = $(this).serialize();
            NProgress.start();
            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: data,
                success: function(result){
                    Modal.closeAdditional();
                    AddressSelect.doneCallback(result);
                    NProgress.done();
                }
            });
            return false;
        });
    },
    initMap: function (params) {
        // Map
        AddressSelect.map = new ymaps.Map('addressSelectMap', {
            center: [AddressSelect.cityLat, AddressSelect.cityLng],
            zoom: 11,
            controls: ['zoomControl']
        }, {
            suppressMapOpenBlock: true
        });

        // Event when click
        AddressSelect.map.events.add('click', function (e) {
            if (AddressSelect.type === 'map'){
                AddressSelect.setMarkerCoords(e.get('coords')[0], e.get('coords')[1]);
                AddressSelect.mapProcess(e.get('coords'));
            }
        });

        if ((params.latValue !== undefined && params.latValue !== 0) &&
            (params.lngValue !== undefined && params.lngValue !== 0)) {
            AddressSelect.setMarkerCoords(params.latValue, params.lngValue);
            AddressSelect.map.setCenter([params.latValue, params.lngValue]);
        }
    },
    initSuggest: function(){
        let SuggestView = new ymaps.SuggestView(AddressSelect.addressInput.attr('id'), {
            provider: {
                suggest: function(request, options) {
                    return ymaps.suggest(AddressSelect.cityName + ', ' + request);
                }
            }
        });
        SuggestView.events.add('select', function(e){
            let address = e.get('item').value;
            ymaps.geocode(address, {
                results: 1
            }).then(function (res) {
                let geoObject = res.geoObjects.get(0);
                if (geoObject !== undefined){
                    let coords = geoObject.geometry._coordinates;
                    AddressSelect.addressValue = address;
                    AddressSelect.latValue = coords[0];
                    AddressSelect.lngValue = coords[1];
                    AddressSelect.setModalFormInfo();

                    AddressSelect.setMarkerCoords(AddressSelect.latValue, AddressSelect.lngValue);
                    AddressSelect.map.setCenter([AddressSelect.latValue, AddressSelect.lngValue]);
                    AddressSelect.setAdditionalFields(geoObject.properties.get('metaDataProperty').GeocoderMetaData.Address.Components)
                }
            });
        });
        AddressSelect.mapSuggest = SuggestView;
    },
    setMarkerCoords: function (lat, lng) {
        if (AddressSelect.mapMarker == null){
            AddressSelect.mapMarker = new ymaps.Placemark([lat, lng], {
                iconCaption: 'Указанная точка'
            }, {
                preset: 'islands#greenDotIconWithCaption',
                draggable: true
            });
            AddressSelect.map.geoObjects.add(AddressSelect.mapMarker);

            // Event when drag
            AddressSelect.mapMarker.events.add('dragend', function(e) {
                AddressSelect.mapProcess(AddressSelect.mapMarker.geometry.getCoordinates());
            });
        }

        AddressSelect.mapMarker.geometry.setCoordinates([lat, lng]);
    },
    setAdditionalFields: function(components){
        let houseValue = '';
        let apartmentValue = '';
        let intercomValue = '';
        let entranceValue = '';
        let floorValue = '';

        $(components).each(function (key, item) {
            if (item['kind'] === 'house'){
                houseValue = item['name'];
            } else if (item['kind'] === 'entrance'){
                entranceValue = item['name'].replace('подъезд ', '');
            }
        });

        AddressSelect.houseInput.val(houseValue);
        AddressSelect.apartmentInput.val(apartmentValue);
        AddressSelect.intercomInput.val(intercomValue);
        AddressSelect.entranceInput.val(entranceValue);
        AddressSelect.floorInput.val(floorValue);
    },
    clearAdditionalFields: function(){
        AddressSelect.houseInput.val('');
        AddressSelect.apartmentInput.val('');
        AddressSelect.intercomInput.val('');
        AddressSelect.entranceInput.val('');
        AddressSelect.floorInput.val('');
    },
    mapProcess: function(coords) {
        AddressSelect.mapMarker.properties.set('iconCaption', 'Поиск...');
        ymaps.geocode(coords).then(function (res) {
            let geoObject = res.geoObjects.get(0);
            if (geoObject !== undefined){
                AddressSelect.addressValue = geoObject.getAddressLine();
                AddressSelect.latValue = coords[0];
                AddressSelect.lngValue = coords[1];
                AddressSelect.setModalFormInfo();
                AddressSelect.mapMarker.properties.set('iconCaption', AddressSelect.addressValue);

                AddressSelect.setAdditionalFields(geoObject.properties.get('metaDataProperty').GeocoderMetaData.Address.Components)
            }
        });
    },
    toggleFields: function () {
        this.addressInputs.show();
        this.customerAddresses.hide();
        this.clearCheckedAddresses();

        if (this.type === 'input'){
            this.addressInput.prop('readonly', false);
            if (this.mapSuggest == null){
                ymaps.ready(this.initSuggest)
            }
            this.addressTitleValue = null;
            if (AddressSelect.mapMarker){
                AddressSelect.mapMarker.options.set('draggable', false)
            }
        } else if (this.type === 'list'){
            this.customerAddresses.show();
            if (this.mapSuggest == null){
                ymaps.ready(this.initSuggest)
            }
            this.addressInputs.hide();
            if (AddressSelect.mapMarker){
                AddressSelect.mapMarker.options.set('draggable', false)
            }
        } else {
            this.addressInput.prop('readonly', true);
            if (this.mapSuggest !== null){
                this.mapSuggest.destroy();
                this.mapSuggest = null;
            }
            this.addressTitleValue = null;
            if (AddressSelect.mapMarker){
                AddressSelect.mapMarker.options.set('draggable', true)
            }
        }
    },
    clearCheckedAddresses: function(){
        this.customerAddresses.find('input:checked').each(function () {
            $(this).prop('checked', false);
        });
    },
    setModalFormInfo: function () {
        this.addressInput.val(this.addressValue);
        this.latInput.val(this.latValue);
        this.lngInput.val(this.lngValue);
        this.addressTitleInput.val(this.addressTitleValue);
    }
}

window.Advert = {
    open: function () {
        $.get(UrlManager.to('advert', 'modal'), function (res) {
            if (res['status'] === 'success'){
                Modal.content(res['content']);
                Modal.open('.modal-main');

                let seconds = 20;
                let button = $('.advert__close');
                let interval = setInterval(function () {
                    if (seconds === 0){
                        button.text('Закрыть');
                        button.prop('disabled', false);
                        clearInterval(interval);

                        $.post(UrlManager.to('advert', 'showed', {id: res['id']}));

                        return;
                    }

                    button.text('Закрыть (' + seconds + ')');
                    seconds--;
                }, 1000);
            }
        });
    }
}