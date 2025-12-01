window.SaleStatus = {
    render: function (data) {
        // Prepare data
        let series = [];

        // Total
        let seriesTotal = {
            name: 'Все заказы',
            data: []
        }
        data['total'].forEach(function (item) {
            seriesTotal.data.push(item['sum']);
        });
        series.push(seriesTotal);

        // Total
        let seriesCancel = {
            name: 'Отмененные',
            data: []
        }
        data['cancel'].forEach(function (item) {
            seriesCancel.data.push(item['sum']);
        });
        series.push(seriesCancel);

        // Total
        let seriesDone = {
            name: 'Завершенные',
            data: []
        }
        data['done'].forEach(function (item) {
            seriesDone.data.push(item['sum']);
        });
        series.push(seriesDone);

        // Prepare options
        let options = {
            series: series,
            grid: {
                borderColor: '#636363',
                xaxis: {
                    lines: {show: true}
                },
                yaxis: {
                    lines: {show: false}
                },
            },
            chart: {
                type: 'line',
                toolbar: {show: false},
                height: 210
            },
            legend: {show: false},
            dataLabels: {
                enabled: false,
                formatter: function (val, opts) {
                    return Chart.numberShort(val);
                },
            },
            stroke: {
                curve: 'smooth',
                width: 1.5
            },
            xaxis: {
                categories: data['categories'],
                labels: {maxHeight: 22}
            },
            yaxis: {
                labels: {
                    maxWidth: 20,
                    formatter: function(val, index) {
                        return Chart.numberShort(val);
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function(value, { series, seriesIndex, dataPointIndex, w }){
                        let category = 'total';
                        if (seriesIndex === 1){
                            category = 'cancel';
                        } else if (seriesIndex === 2){
                            category = 'done';
                        }
                        return data[category][dataPointIndex]['sum_label'];
                    }
                },
                x: {
                    formatter: function(value, { series, seriesIndex, dataPointIndex, w }){
                        let category = 'total';
                        if (seriesIndex === 1){
                            category = 'cancel';
                        } else if (seriesIndex === 2){
                            category = 'done';
                        }
                        return data[category][dataPointIndex]['date'];
                    }
                },
            },
            colors: ['#4272A7', '#D84538', '#C4D55A'],
            fill: {opacity: 1},
        };

        // Loader hide
        Chart.loaderHide('.sale-status__chart');

        // Render
        let chart = new ApexCharts(document.querySelector('.sale-status__chart'), options);
        chart.render();
    }
}

window.SaleMonth = {
    chart: null,
    renderBefore: function(){
        // Clear chart
        if (SaleMonth.chart) SaleMonth.chart.destroy();
    },
    render: function (data) {
        // Prepare data
        let series = [];
        let categories = [];
        data.forEach(function (item) {
            series.push(item['sum']);
            categories.push(item['name']);
        });

        // Prepare options
        let options = {
            series: [
                {
                    name: 'Продажи',
                    data: series
                }
            ],
            grid: {show: false},
            chart: {
                type: 'bar',
                toolbar: {show: false},
                height: 190,
                parentHeightOffset: 0
            },
            dataLabels: {enabled: false},
            stroke: {
                curve: 'smooth',
                width: 1.5
            },
            xaxis: {
                categories: categories,
                labels: {
                    maxHeight: 40,
                    rotate: -90
                }
            },
            yaxis: {
                labels: {
                    maxWidth: 30,
                    formatter: function(val, index) {
                        return Chart.numberShort(val);
                    }
                },
                tooltip: {enabled: false}
            },
            tooltip: {
                y: {
                    formatter: function(value, { series, seriesIndex, dataPointIndex, w }){
                        return data[dataPointIndex]['sum_label'];
                    }
                },
                x: {
                    formatter: function(value, { series, seriesIndex, dataPointIndex, w }){
                        return data[dataPointIndex]['month'];
                    }
                },
            },
            colors: ['#CAD294'],
            fill: {opacity: 1},
        };

        // Render
        SaleMonth.chart = new ApexCharts(document.querySelector('.sale-month__chart'), options);
        SaleMonth.chart.render();
    },
}