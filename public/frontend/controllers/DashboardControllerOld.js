app.controller('DashboardController', function ($scope, ApiService) {

    $scope.data = {};

    ApiService.getDashboard().then(res => {
        $scope.data = res.data;
    });

    ApiService.getDashboardCharts().then(res => {
        drawCharts(res.data);
    });

    function drawCharts(data) {

        // SALES CHART
        new Chart(document.getElementById('salesChart'), {
            type: 'bar',
            data: {
                labels: data.months,
                datasets: [{
                    label: 'Sales Amount',
                    data: data.sales,
                    backgroundColor: '#0d6efd'
                }]
            }
        });

        // PROFIT CHART
        new Chart(document.getElementById('profitChart'), {
            type: 'line',
            data: {
                labels: data.months,
                datasets: [{
                    label: 'Profit',
                    data: data.profit,
                    borderColor: '#198754',
                    fill: false,
                    tension: 0.2
                }]
            }
        });
    }
});
