app.controller('SalesController', function ($scope, ApiService) {

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

        console.log('Sending sale:', angular.copy($scope.sale));

        ApiService.saveSale($scope.sale)
            .then(function (res) {
                alert(res.data.message || 'Sale Saved');
                $scope.sale.items = [];
                $scope.addItem();
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

    $scope.addItem();
});
