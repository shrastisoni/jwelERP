app.controller('PurchaseListController', function ($scope, ApiService, $location) {

    $scope.purchases = [];
    $scope.parties = [];

    $scope.filters = {
        from_date: '',
        to_date: '',
        party_id: '',
        search: ''
    };

    loadParties();
    load();

    function loadParties() {
        ApiService.getSuppliers().then(res => {
            $scope.parties = res.data;
        });
    }

    function load() {
        ApiService.getPurchases($scope.filters)
            .then(res => {
                $scope.purchases = res.data;
            });
    }

    $scope.resetFilters = function () {
        $scope.filters = {};
        load();
    };

    $scope.totalAmount = function () {
        let total = 0;
        angular.forEach($scope.purchases, function (p) {
            total += parseFloat(p.total_amount);
        });
        return total;
    };

    $scope.view = function (id) {
        $location.path('/purchase/view/' + id);
    };

    $scope.remove = function (id) {
        if (!confirm('Delete purchase?')) return;

        ApiService.deletePurchase(id).then(() => load());
    };

});
