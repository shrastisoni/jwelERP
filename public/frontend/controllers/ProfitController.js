app.controller('ProfitController', function ($scope, ApiService) {

    $scope.products = [];

    ApiService.getProductProfit().then(res => {
        $scope.products = res.data;
    });

    $scope.rows = [];

    ApiService.getPurchaseCostProfit().then(res => {
        $scope.rows = res.data;
    });

     $scope.fiforows = [];

    ApiService.getFifoProfit().then(res => {
        $scope.fiforows = res.data;
    });
});
app.controller('InventoryController', function ($scope, ApiService) {

    ApiService.getInventory().then(res => {
        $scope.rows = res.data;
    });

    $scope.ledger = function (p) {
        window.location.hash = '#!/stock-ledger/' + p.id;
    };

    $scope.adjust = function () {
        window.location.hash = '#!/stock-adjustment'; 
    };


    ApiService.getLowStockIn().then(res => {
        $scope.lowStockCount = res.data.length;
    });

});
