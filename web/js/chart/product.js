window.ProductCategory = {
    chart: null,
    renderBefore: function(res){
        // Clear chart
        if (ProductCategory.chart) ProductCategory.chart.destroy();

        ProductCategory.render(res);
    },
    render: function (data) {
        // Prepare data
        let series = [];
        let labels = [];
        data.forEach(function (item) {
            series.push(item['sum']);
            labels.push(item['name']);
        });

        // Prepare options
        let options = {
            series: series,
            chart: {
                type: 'pie',
                height: 207,
                parentHeightOffset: 0
            },
            labels: labels,
            legend: {
                width: 150,
                floating: true
            },
            stroke: {width: 0},
            dataLabels: {enabled: false},
            colors: ['#4271A6', '#A0785A', '#BEBEBE', '#E8C13A', '#929F42', '#7F5680', '#5F3F7C'],
            fill: {opacity: 1},
        };

        // Render
        ProductCategory.chart = new ApexCharts(document.querySelector('.product-category__chart'), options);
        ProductCategory.chart.render();
    }
}