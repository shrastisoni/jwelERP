// app.controller('DashboardController', function ($scope, ApiService) {

//     $scope.summary = {};
    
//     ApiService.getDashboard().then(function (res) {
//         $scope.summary = res.data;
//     });

//     ApiService.getRecentSales().then(function (res) {
//         $scope.recentSales = res.data;
//     });

//     ApiService.getLowStock().then(function (res) {
//         $scope.lowStock = res.data;
//     });
//     ApiService.getDashboardCharts().then(res => {
//         // $scope.summary = res.data.summary;
//         renderChart(
//             'salesChart',
//             res.data.sales,
//             'Sales'
//         );

//         renderChart(
//             'profitChart',
//             res.data.profit,
//             'Profit'
//         );
//     });

//     function renderChart(id, data, label) {
//         new Chart(document.getElementById(id), {
//             type: 'line',
//             data: {
//                 labels: data.labels,
//                 datasets: [{
//                     label: label,
//                     data: data.values,
//                     borderWidth: 2,
//                     fill: false
//                 }]
//             }
//         });
//     }
// });
app.controller('DashboardController', function ($scope, $rootScope, $timeout, ApiService) {

    $rootScope.pageTitle = 'Dashboard';

    $scope.summary = {};

    ApiService.getDashboard().then(res => {
        $scope.summary = res.data;
    });

    ApiService.getDashboardCharts().then(res => {
        $timeout(() => {
            drawChart('salesChart', res.data.sales, 'Monthly Sales');
            drawChart('profitChart', res.data.profit, 'Monthly Profit');
        });
    });

    let charts = {};

    function drawChart(id, data, label) {

        if (charts[id]) {
            charts[id].destroy();
        }

        charts[id] = new Chart(document.getElementById(id), {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: label,
                    data: data.values,
                    borderWidth: 2,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
});
