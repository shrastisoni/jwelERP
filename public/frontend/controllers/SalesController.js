app.controller('SalesController', function ($scope, ApiService, $location) {

    $scope.sale = {
        party_id: null,
        invoice_date: new Date(),
        items: []
    };

    $scope.parties = [];
    $scope.products = [];

    ApiService.getCustomers().then(res => {
        $scope.parties = res.data;
    });

    ApiService.getProducts().then(res => {
        $scope.products = res.data;
    });

    $scope.addItem = function () {
        $scope.sale.items.push({
            product_id: null,
            quantity: 1,
            weight: 0.01,
            rate: 1
        });
    };

    $scope.removeItem = function (index) {
        $scope.sale.items.splice(index, 1);
    };

    $scope.totalAmount = function () {
        let total = 0;
        angular.forEach($scope.sale.items, function (i) {
            total += (i.weight * i.rate) || 0;
        });
        return total;
    };

    $scope.saveSale = function () {
       ApiService.saveSale($scope.sale)
            .then(function (res) {
                alert(res.data.message || 'Sale Saved');
                $scope.sale.items = [];
                $scope.addItem();
                $location.path('/sales');
            })
            .catch(function (err) {
                console.error(err);
                alert(
                    err.data?.message ||
                    JSON.stringify(err.data?.errors) ||
                    'Error saving sale'
                );
            });
    };
    $scope.changeLocation = function(){
        $location.path('/sales');
    };
    $scope.addItem();
});

app.controller('SalesListController', function ($scope, ApiService, $location) {

    $scope.sales = [];
    $scope.customers = [];

    $scope.filters = {};

    ApiService.getCustomers().then(res => {
        $scope.customers = res.data;
    });

    $scope.load = function () {
        ApiService.getSales($scope.filters).then(res => {
            $scope.sales = res.data;
        });
    };

    $scope.reset = function () {
        $scope.filters = {};
        $scope.load();
    };

    $scope.totalAmount = function () {
        return $scope.sales.reduce((t, s) => t + parseFloat(s.total_amount), 0);
    };

    $scope.view = function (id) {
        $location.path('/sale/view/' + id);
    };

    $scope.load();
});
// app.controller('SaleViewController', function ($scope, $routeParams, ApiService) {

//     ApiService.getSale($routeParams.id).then(res => {
//         $scope.sale = res.data;
//     });

// });

app.controller('SaleViewController', function ($scope, $routeParams, ApiService) {

    $scope.sale = {};
    $scope.totalWeightOut = 0;

    ApiService.getSale($routeParams.id).then(function (res) {
        $scope.sale = res.data;

        $scope.totalWeightOut = $scope.sale.items.reduce(
            (t, i) => t + parseFloat(i.weight), 0
        );
    });

});
