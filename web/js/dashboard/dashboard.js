/* Dashboard
----------------------------------------*/
let Dashboard = {

    payload: null,
    charts: [],

    palettes: {
        light: {
            series: ['#2a78d6', '#eb6834'],
            orders: ['#2a78d6', '#e34948', '#1baf7a'],
            bar: '#2a78d6',
            funnel: ['#86b6ef', '#5598e7', '#2a78d6', '#1c5cab']
        },
        dark: {
            series: ['#3987e5', '#d95926'],
            orders: ['#3987e5', '#e66767', '#199e70'],
            bar: '#3987e5',
            funnel: ['#9ec5f4', '#6da7ec', '#3987e5', '#256abf']
        }
    },

    init: function (payload) {
        this.payload = payload;
        this.render();
        this.observeTheme();
    },

    render: function () {
        let theme = this.theme();
        let palette = this.palettes[theme];
        let charts = this.payload.charts;
        let labels = this.payload.labels;

        this.line('dashboard-registrations-by-day', charts.registrationsByDay.categories, [
            {name: labels.customers, data: charts.registrationsByDay.customers},
            {name: labels.executors, data: charts.registrationsByDay.executors}
        ], palette.series, 300, 'top');

        this.line('dashboard-orders-by-day', charts.ordersByDay.categories, [
            {name: labels.allOrders, data: charts.ordersByDay.created},
            {name: labels.cancelledOrders, data: charts.ordersByDay.cancelled},
            {name: labels.completedOrders, data: charts.ordersByDay.completed}
        ], palette.orders, 300, 'top');

        this.column('dashboard-bids-by-day', charts.bidsByDay.categories, {
            name: labels.bids,
            data: charts.bidsByDay.bids
        }, palette.bar, 280);

        this.bars('dashboard-funnel', charts.funnel.labels, {
            name: labels.orders,
            data: charts.funnel.values
        }, palette.funnel, true, 280);

        this.bars('dashboard-orders-by-type', charts.ordersByType.labels, {
            name: labels.orders,
            data: charts.ordersByType.values
        }, [palette.bar], false, 280);

        this.bars('dashboard-orders-by-status', charts.ordersByStatus.labels, {
            name: labels.orders,
            data: charts.ordersByStatus.values
        }, [palette.bar], false, 280);

        this.bars('dashboard-top-locations', charts.topLocations.labels, {
            name: labels.orders,
            data: charts.topLocations.values
        }, [palette.bar], false, 320);
    },

    line: function (id, categories, series, colors, height, legendPosition) {
        this.make(id, Object.assign(this.common(height), {
            chart: this.chartBase('line', height),
            series: series,
            colors: colors,
            dataLabels: {enabled: false},
            stroke: {width: 2, curve: 'smooth'},
            markers: {size: 0, hover: {size: 4}},
            xaxis: this.timeAxis(categories),
            yaxis: {labels: {formatter: this.integer}},
            legend: {
                show: true,
                position: legendPosition || 'bottom',
                horizontalAlign: legendPosition === 'top' ? 'right' : 'center'
            }
        }));
    },

    column: function (id, categories, series, color, height) {
        this.make(id, Object.assign(this.common(height), {
            chart: this.chartBase('bar', height),
            series: [series],
            colors: [color],
            dataLabels: {enabled: false},
            plotOptions: {bar: {columnWidth: '60%', borderRadius: 2}},
            xaxis: this.timeAxis(categories),
            yaxis: {labels: {formatter: this.integer}},
            legend: {show: false}
        }));
    },

    bars: function (id, categories, series, colors, distributed, height) {
        let max = Math.max.apply(null, series.data.concat([0]));

        this.make(id, Object.assign(this.common(height), {
            chart: this.chartBase('bar', height),
            series: [series],
            colors: colors,
            plotOptions: {
                bar: {
                    horizontal: true,
                    distributed: distributed,
                    borderRadius: 2,
                    barHeight: '65%',
                    dataLabels: {position: 'top'}
                }
            },
            dataLabels: {
                enabled: true,
                textAnchor: 'start',
                offsetX: 28,
                formatter: this.integer,
                style: {colors: [this.ink()], fontWeight: 600}
            },
            xaxis: {
                categories: categories,
                max: max > 0 ? Math.ceil(max * 1.15) : undefined,
                labels: {formatter: this.integer}
            },
            yaxis: {labels: {maxWidth: 180}},
            legend: {show: false}
        }));
    },

    make: function (id, options) {
        let element = document.getElementById(id);

        if (!element) {
            return;
        }

        let chart = new ApexCharts(element, options);
        chart.render();
        this.charts.push(chart);
    },

    common: function (height) {
        return {
            grid: {borderColor: this.cssVar('--theme-border')},
            tooltip: {theme: this.theme()},
            noData: {text: this.payload.labels.noData || ''}
        };
    },

    chartBase: function (type, height) {
        return {
            type: type,
            height: height,
            fontFamily: 'inherit',
            foreColor: this.cssVar('--theme-text-muted'),
            toolbar: {show: false},
            animations: {speed: 400}
        };
    },

    timeAxis: function (categories) {
        return {
            categories: categories,
            tickAmount: Math.min(categories.length, 8),
            labels: {rotate: 0, hideOverlappingLabels: true, trim: false}
        };
    },

    integer: function (value) {
        return value === null || value === undefined ? '' : Math.round(value).toString();
    },

    theme: function () {
        return document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
    },

    ink: function () {
        return this.cssVar('--theme-text');
    },

    cssVar: function (name) {
        return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
    },

    observeTheme: function () {
        let self = this;

        new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                if (mutation.attributeName === 'data-theme') {
                    self.rebuild();
                }
            });
        }).observe(document.documentElement, {attributes: true});
    },

    rebuild: function () {
        this.charts.forEach(function (chart) {
            chart.destroy();
        });
        this.charts = [];
        this.render();
    }

};
