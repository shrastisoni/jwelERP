app.controller('StockLedgerController', function ($scope, ApiService) {

    $scope.ledgers = [];

    ApiService.getStockLedger().then(function (res) {
        $scope.ledgers = res.data;
    });
});
