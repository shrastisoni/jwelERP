// app.controller('StockLedgerController', function ($scope, ApiService) {

//     $scope.ledgers = [];

//     ApiService.getStockLedger().then(function (res) {
//         $scope.ledgers = res.data;
//     });
// });
app.controller('StockLedgerController', function ($scope, ApiService) {

    $scope.ledger = [];
    $scope.products = [];
    $scope.filters = {};
    $scope.totals = {
        in: 0,
        out: 0,
        closing: 0
    };

    // LOAD PRODUCTS
    ApiService.getProducts().then(res => {
        $scope.products = res.data;
    });

    // LOAD LEDGER
    $scope.load = function () {
        ApiService.getStockLedger($scope.filters).then(res => {
            $scope.ledger = res.data;
            calculateTotals();
        });
    };

    function calculateTotals() {
        let inW = 0, outW = 0;

        $scope.ledger.forEach(r => {
            inW += parseFloat(r.weight_in || 0);
            outW += parseFloat(r.weight_out || 0);
        });

        $scope.totals.in = inW;
        $scope.totals.out = outW;

        if ($scope.ledger.length) {
            $scope.totals.closing =
                $scope.ledger[$scope.ledger.length - 1].balance_weight;
        }
    }

    $scope.load();
});
app.controller('StockValuationController', function ($scope, ApiService) {

    $scope.items = [];
    $scope.total = 0;

    ApiService.getStockValuation().then(res => {
        $scope.items = res.data.items;
        $scope.total = res.data.total_value;
    });

});
