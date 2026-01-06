app.controller('OpeningStockController', function ($scope, ApiService) {

    $scope.products = [];
    $scope.form = {
        product_id: null,
        weight: 0,
        rate: 0
    };

    ApiService.getProducts().then(res => {
        $scope.products = res.data;
    });

    $scope.save = function () {
        ApiService.addOpeningStock($scope.form)
            .then(res => {
                alert(res.data.message);
                $scope.form = { product_id: null, weight: 0, rate: 0 };
            })
            .catch(err => {
                alert(
                    err.data?.errors
                        ? Object.values(err.data.errors)[0][0]
                        : err.data.message
                );
            });
    };
});
