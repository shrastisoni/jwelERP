app.controller('ProductController', function ($scope, ApiService) {

    ApiService.getProducts().then(function (res) {
        $scope.products = res.data;
    });
});
