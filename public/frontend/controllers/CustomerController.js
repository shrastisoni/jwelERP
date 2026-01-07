app.controller('CustomerController', function ($scope, ApiService) {

    $scope.customers = [];
    $scope.customer = {};
    $scope.editing = false;
    $scope.search = '';

    load();

    function load() {
        ApiService.getCustomers($scope.search).then(res => {
            $scope.customers = res.data;
        });
    }

    $scope.save = function () {

        if (!$scope.customer.name) {
            alert('Customer name required');
            return;
        }

        if ($scope.editing) {
            ApiService.updateCustomer($scope.customer.id, $scope.customer)
                .then(reset);
        } else {
            ApiService.addCustomer($scope.customer)
                .then(reset);
        }
    };

    $scope.edit = function (c) {
        $scope.customer = angular.copy(c);
        $scope.editing = true;
    };

    $scope.delete = function (c) {
        if (!confirm('Delete customer?')) return;

        ApiService.deleteCustomer(c.id)
            .then(load);
    };

    $scope.searchCustomers = function () {
        load();
    };

    function reset() {
        $scope.customer = {};
        $scope.editing = false;
        load();
    }
});
