'use strict';

// Moment
moment.locale('ru');

// Variables
const DATE_FORMAT = 'YYYY-MM-DD';

/* Document ready
----------------------------------------*/
$(function () {
    // Global click
    $(window).on('click', function (e) {
        if (e.target.closest('.dropdown') == null){
            Dropdown.hideAll();
        }
    });

    // Dropdown
    $(window).on('click', '.dropdown__toggle', function () {
        $(this).parents('.dropdown').toggleClass('dropdown--open');
    });
})

/* Dropdown
----------------------------------------*/
window.Dropdown = {
    show: function (className) {
        $(className).addClass('dropdown--open');
    },
    hide: function (className) {
        $(className).removeClass('dropdown--open');
    },
    hideAll: function () {
        $('.dropdown--open').removeClass('dropdown--open');
    }
}

/* Chart
----------------------------------------*/
window.Chart = {
    // Init
    init: function () {
        Chart.initOrderObjects();
        Chart.initSaleObjects();
        Chart.initChatObjects();
        Chart.initProductsObjects();
        Chart.initDeliveryObjects();
    },
    initOperator: function () {
        Chart.initOperatorObjects();
    },
    initDashboard: function () {
        Chart.initDashboardObjects();
    },
    initDashboardObjects: function (){
        // Sale plan
        new Widget({
            parent: '.sale-plan',
            container: '.sale-plan__body',
            url: '/chart/sale-plan'
        }, true);

        // Order delivery
        new Widget({
            parent: '.order-delivery',
            container: '.order-delivery__body',
            url: '/chart/order-delivery',
            searchParams: {
                'date_from': moment().startOf('month'),
                'date_to': moment().endOf('month'),
            },
        }, true);

        // Order cancel
        new Widget({
            parent: '.order-cancel',
            container: '.order-cancel__body',
            url: '/chart/order-cancel',
            searchParams: {
                'date_from': moment().startOf('month'),
                'date_to': moment().endOf('month'),
            },
        }, true);
    },
    initOrderObjects: function () {
        // Order handler
        new Widget({
            parent: '.order-handler',
            url: '/chart/order-handler',
            searchParams: {
                'date_from': moment(),
                'date_to': moment(),
            },
            callbackLoad: OrderHandler.render,
            callbackBefore: OrderHandler.renderBefore
        }, true);

        // Order average handle
        new Widget({
            parent: '.order-average-handle',
            container: '.order-average-handle__body',
            url: '/chart/order-average-handle',
            searchParams: {
                'date_from': moment(),
                'date_to': moment(),
            },
        }, true);

        // Order completed
        new Widget({
            parent: '.order-completed',
            url: '/chart/order-completed',
            searchParams: {
                'date_from': moment(),
                'date_to': moment(),
            },
            callbackLoad: OrderCompleted.render,
            callbackBefore: OrderCompleted.renderBefore
        }, true);
    },
    initSaleObjects: function () {
        // Sale channels
        new Widget({
            parent: '.sale-channels',
            container: '.sale-channels__body',
            url: '/chart/sale-channels',
            searchParams: {
                'date_from': moment().startOf('month'),
                'date_to': moment().endOf('month'),
            },
        }, true);

        // Sale status
        new Widget({
            parent: '.sale-status',
            container: '.sale-status__chart',
            url: '/chart/sale-status',
            searchParams: {
                'date_from': moment().subtract(6, 'days'),
                'date_to': moment(),
            },
            callbackLoad: SaleStatus.render
        }, true);

        // Sale operator
        new Widget({
            parent: '.sale-operator',
            container: '.sale-operator__body',
            url: '/chart/sale-operator',
            searchParams: {
                'date_from': moment(),
                'date_to': moment(),
            },
        }, true);

        // Sale month
        new Widget({
            parent: '.sale-month',
            container: '.sale-month__chart',
            url: '/chart/sale-month',
            searchParams: {
                'date_from': moment().subtract(11, 'month').startOf('month'),
                'date_to': moment(),
            },
            callbackLoad: SaleMonth.render,
            callbackBefore: SaleMonth.renderBefore
        }, true);
    },
    initChatObjects: function () {
        // Chat Count
        new Widget({
            parent: '.chat-count',
            container: '.chat-count__body',
            url: '/chart/chat-count',
            searchParams: {
                'date_from': moment(),
                'date_to': moment(),
            },
        }, true);
    },
    initProductsObjects: function () {
        // Product category
        new Widget({
            parent: '.product-category',
            container: '.product-category__chart',
            url: '/chart/product-category',
            searchParams: {
                'date_from': moment().startOf('month'),
                'date_to': moment().endOf('month'),
            },
            callbackLoad: ProductCategory.renderBefore,
        }, true);
    },
    initDeliveryObjects: function () {
        // Delivery average
        new Widget({
            parent: '.delivery-average',
            container: '.delivery-average__body',
            url: '/chart/delivery-average',
            searchParams: {
                'date_from': moment(),
                'date_to': moment(),
            },
        }, true);
    },
    initOperatorObjects: function () {
        // Long handle
        new Widget({
            parent: '.long-handle',
            container: '.long-handle__body',
            url: '/chart/long-handle',
            searchParams: {
                'date_from': moment().subtract(2, 'days'),
                'date_to': moment(),
            },
        }, true);

        // Without attention
        new Widget({
            parent: '.without-attention',
            container: '.without-attention__body',
            url: '/chart/without-attention',
            searchParams: {
                'date_from': moment().subtract(2, 'days'),
                'date_to': moment(),
            },
        }, true);

        // Express search
        new Widget({
            parent: '.express-search',
            container: '.express-search__body',
            url: '/chart/express-search',
            searchParams: {
                'date_from': moment().subtract(2, 'days'),
                'date_to': moment(),
            },
        }, true);

        // Express long
        new Widget({
            parent: '.express-long',
            container: '.express-long__body',
            url: '/chart/express-long',
            searchParams: {
                'date_from': moment().subtract(2, 'days'),
                'date_to': moment(),
            },
        }, true);

        // Standard long
        new Widget({
            parent: '.standard-long',
            container: '.standard-long__body',
            url: '/chart/standard-long',
            searchParams: {
                'date_from': moment().subtract(2, 'days'),
                'date_to': moment(),
            },
        }, true);
    },

    // Methods
    random: function (from, to) {
        return Math.floor(Math.random() * to) + from;
    },
    getPeriodName: function (period) {
        if (period === 'today'){
            return 'За сегодня';
        }
        if (period === 'week'){
            return 'За эту неделю';
        }
        if (period === 'month'){
            return 'За этот месяц';
        }

        return 'За период';
    },
    numberShort: function(val){
        if (val <= 999){
            return val;
        }
        if (val <= 999999){
            return Math.floor(val / 1000) + 'K';
        }
        if (val <= 999999999){
            return Math.floor(val / 1000000) + 'M';
        }
        if (val <= 999999999999){
            return Math.floor(val / 1000000000) + 'B';
        }
        return val;
    },

    // Loader
    loaderShow: function (elem) {
        $(elem).html('').addClass('chart-loader');
    },
    loaderHide: function (elem) {
        $(elem).removeClass('chart-loader');
    },
}

class Widget {
    // Constructor
    constructor(options, run = false) {
        this.parent = options['parent'];
        this.container = options['container'];
        this.url = options['url'];
        this.searchParams = options['searchParams'];
        this.callbackBefore = options['callbackBefore'];
        this.callbackLoad = options['callbackLoad'];

        if (run){
            this.init();
            this.load();
        }
    }

    // Init
    init(){
        this.initInputs();
        this.setDatesDefault();
        this.setDatesLabel();
    }
    initInputs(){
        let block = $(this.parent);
        let periodInput = block.find('.chart-period__input');

        if (!periodInput.length){
            return;
        }

        // Date range picker
        let opens = periodInput.data('opens');
        if (opens === undefined){
            opens = 'right';
        }
        let drops = periodInput.data('drops');
        if (drops === undefined){
            drops = 'down';
        }
        periodInput.daterangepicker({
            ranges: {
                'Сегодня': [moment(), moment()],
                'Вчера': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'За 7 дней': [moment().subtract(6, 'days'), moment()],
                'За 30 дней': [moment().subtract(29, 'days'), moment()],
                'Этот месяц': [moment().startOf('month'), moment().endOf('month')],
                'Посл месяц': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            },
            locale: {
                format: 'DD.MM.YYYY',
                separator: ' - ',
                applyLabel: 'Применить',
                cancelLabel: 'Отмена',
                fromLabel: 'От',
                toLabel: 'в',
                customRangeLabel: 'Пользовательский',
                weekLabel: 'W',
                firstDay: 1
            },
            autoUpdateInput: false,
            alwaysShowCalendars: true,
            opens: opens,
            drops: drops,
            parentEl: this.parent + ' .chart-period'

        }).on('apply.daterangepicker', (ev, picker) => {
            this.setSearchParam('date_from', picker.startDate);
            this.setSearchParam('date_to', picker.endDate);
            this.setDatesLabel();
            this.load();
        });
    }

    // Methods
    load(){
        // Loader show
        Chart.loaderShow(this.container);

        // Callback before
        if (typeof this.callbackBefore == 'function'){
            this.callbackBefore();
        }

        $.get(this.getUrl(), (res) => {
            if (typeof this.callbackLoad == 'function'){
                this.callbackLoad(res);
            } else {
                $(this.container).html(res);
            }

            // Loader hide
            Chart.loaderHide(this.container);

            // Refresh
            this.refresh();
        })
    }
    refresh(){
        let url = new URL(location.href);
        let seconds = parseInt(url.searchParams.get('refresh'));
        if (!seconds){
            return;
        }

        setTimeout(() => {
            this.load()
        }, seconds * 1000);
    }

    // Getters
    getUrl(){
        let url = new URL(this.url, location.origin);

        for (const [key, value] of Object.entries(this.searchParams)){
            if (value instanceof moment){
                url.searchParams.set(key, value.format(DATE_FORMAT));
            } else {
                url.searchParams.set(key, String(value));
            }
        }

        return url.href;
    }
    getDateFrom(){
        if (typeof this.searchParams !== 'object'){
            return undefined;
        }

        return this.searchParams['date_from'];
    }
    getDateTo(){
        if (typeof this.searchParams !== 'object'){
            return undefined;
        }

        return this.searchParams['date_to'];
    }

    // Setters
    setDatesDefault(){
        let dateFrom = this.getDateFrom();
        if (dateFrom === undefined){
            this.setSearchParam('date_from', moment());
        }

        let dateTo = this.getDateTo();
        if (dateTo === undefined){
            this.setSearchParam('date_to', moment());
        }
    }
    setDatesLabel(){
        let dateFrom = this.getDateFrom();
        let dateTo = this.getDateTo();
        let result = dateFrom.format('D MMMM') + ' - ' + dateTo.format('D MMMM');
        let today = moment();

        if (dateFrom.isSame(today, 'day') && dateTo.isSame(today, 'day')){
            result = 'Сегодня';
        } else if (dateFrom.isSame(dateTo, 'day')){
            result = dateFrom.format('D MMMM');
        } else if (dateFrom.isSame(dateTo, 'month')){
            result = dateFrom.format('D') + '-' + dateTo.format('D MMMM');
        }

        $(this.parent).find('.chart-period__value').text(result);
    }
    setSearchParam(key, value){
        if (typeof this.searchParams !== 'object'){
            this.searchParams = {};
        }

        this.searchParams[key] = value;
    }
}