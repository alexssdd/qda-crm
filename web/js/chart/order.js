window.OrderHandler = {
    chart: null,
    renderBefore: function(){
        // Loader show
        $('.order-handler__body').addClass('chart-loader');

        // Clear chart
        if (OrderHandler.chart) OrderHandler.chart.destroy();
    },
    render: function (data) {
        // Prepare data
        let length = 360 * data['percent'] / 100;
        let startAngle = 0 - length / 2;
        let endAngle = 360 - length / 2;

        // Set counts
        $('.order-handler__bot').text(data['count_bot']);
        $('.order-handler__operator').text(data['count_operator']);

        // Prepare options
        let options = {
            series: [data['percent']],
            chart: {
                type: 'radialBar',
                width: '200px',
            },
            plotOptions: {
                radialBar: {
                    startAngle: startAngle,
                    endAngle: endAngle,
                    hollow: {
                        size: '80px',
                        margin: 0,
                    },
                    track: {
                        background: '#757575',
                        margin: 0
                    },
                    dataLabels: {
                        value: {show: false},
                        name: {offsetY: 10},
                    }
                },
            },
            labels: [data['count_total']],
            colors: ['#BFD73E'],
            fill: {opacity: 1},
        };

        // Loader hide
        Chart.loaderHide('.order-handler__body');

        // Render
        OrderHandler.chart = new ApexCharts(document.querySelector('.order-handler__chart'), options);
        OrderHandler.chart.render();
    }
}

window.OrderCompleted = {
    chart: null,
    renderBefore: function(){
        // Loader show
        $('.order-completed__body').addClass('chart-loader');

        // Clear chart
        if (OrderCompleted.chart) OrderCompleted.chart.destroy();
    },
    render: function (data) {
        // Prepare data
        let length = 360 * data['percent'] / 100;
        let startAngle = 0 - length / 2;
        let endAngle = 360 - length / 2;

        // Set counts
        $('.order-completed__done').text(data['count_done']);
        $('.order-completed__cancel').text(data['count_cancel']);

        // Prepare options
        let options = {
            series: [data['percent']],
            chart: {
                type: 'radialBar',
                width: '200px',
            },
            plotOptions: {
                radialBar: {
                    startAngle: startAngle,
                    endAngle: endAngle,
                    hollow: {
                        size: '80px',
                        margin: 0,
                    },
                    track: {
                        background: '#CB544D',
                        margin: 0
                    },
                    dataLabels: {
                        value: {show: false},
                        name: {offsetY: 10},
                    }
                },
            },
            labels: [data['count_total']],
            colors: ['#BFD73E'],
            fill: {opacity: 1},
        };

        // Loader hide
        Chart.loaderHide('.order-completed__body');

        // Render
        OrderCompleted.chart = new ApexCharts(document.querySelector('.order-completed__chart'), options);
        OrderCompleted.chart.render();
    }
}