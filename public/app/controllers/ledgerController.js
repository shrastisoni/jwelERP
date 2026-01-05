app.controller('LedgerController', function($scope, ApiService) {

    ApiService.get('stock-ledger').then(function(response) {
        $scope.ledger = response.data;
    });

});
