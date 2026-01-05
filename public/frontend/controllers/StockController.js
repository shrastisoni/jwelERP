app.controller('StockController', function ($scope, ApiService) {

    ApiService.getStock().then(function (res) {
        $scope.stock = res.data;
    });
});
