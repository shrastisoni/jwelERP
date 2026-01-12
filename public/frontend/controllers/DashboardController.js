app.controller('DashboardController', function ($scope, ApiService) {

    $scope.summary = {};
    
    ApiService.getDashboard().then(function (res) {
        $scope.summary = res.data;
        console.log($scope.summary);
    });

    ApiService.getRecentSales().then(function (res) {
        $scope.recentSales = res.data;
        console.log( $scope.recentSales);
    });

    ApiService.getLowStock().then(function (res) {
        $scope.lowStock = res.data;
         console.log($scope.lowStock);
    });
    ApiService.getDashboardCharts().then(res => {
        // $scope.summary = res.data.summary;
        console.log(res.data);
        renderChart(
            'salesChart',
            res.data.sales,
            'Sales'
        );

        renderChart(
            'profitChart',
            res.data.profit,
            'Profit'
        );
    });

    function renderChart(id, data, label) {
        new Chart(document.getElementById(id), {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: label,
                    data: data.values,
                    borderWidth: 2,
                    fill: false
                }]
            }
        });
    }
});
