app.controller('ProfitController', function ($scope, ApiService) {

    $scope.products = [];

    ApiService.getProductProfit().then(res => {
        $scope.products = res.data;
    });

    $scope.rows = [];

    ApiService.getPurchaseCostProfit().then(res => {
        $scope.rows = res.data;
    });
});
