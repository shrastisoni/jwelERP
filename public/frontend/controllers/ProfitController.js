app.controller('ProfitController', function ($scope, ApiService) {

    $scope.profits = [];

    ApiService.getProductProfit().then(res => {
        $scope.profits = res.data;
        // Object.keys(res.data).forEach((value, index) => {
        //     console.log(typeof(value));
        //     console.log(`Index ${index}: Value ${value}`);
        // });
    });

    $scope.rows = [];

    ApiService.getPurchaseCostProfit().then(res => {
        $scope.rows = res.data;
    });
});
