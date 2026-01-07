app.controller('CustomerLedgerController', function ($scope, $routeParams, ApiService) {

    $scope.customer = {};
    $scope.ledger = [];
    $scope.closing_balance = 0;

    load();

    function load() {
        ApiService.getCustomerLedger($routeParams.customerId)
            .then(res => {
                $scope.customer = res.data.customer;
                $scope.ledger = res.data.ledger;
                $scope.closing_balance = res.data.closing_balance;
            });
    }
});
