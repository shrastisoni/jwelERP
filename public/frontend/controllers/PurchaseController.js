app.controller('PurchaseController', function ($scope, ApiService) {

    $scope.purchase = {
        party_id: null,
        invoice_date: new Date(),
        items: []
    };

    $scope.parties = [];
    $scope.products = [];

    ApiService.getSuppliers().then(r => $scope.parties = r.data);
    ApiService.getProducts().then(r => $scope.products = r.data);

    $scope.addItem = function () {
        $scope.purchase.items.push({
            product_id: null,
            quantity: 1,
            weight: 0,
            rate: 0
        });
    };

    $scope.removeItem = function (i) {
        $scope.purchase.items.splice(i, 1);
    };

    $scope.totalAmount = function () {
        var t = 0;
        angular.forEach($scope.purchase.items, function (i) {
            t += (i.weight * i.rate) || 0;
        });
        return t;
    };

    $scope.savePurchase = function () {
        ApiService.savePurchase($scope.purchase)
            .then(() => alert('Purchase Saved'))
            .catch(e => alert(e.data.message));
    };

    $scope.addItem();
});
