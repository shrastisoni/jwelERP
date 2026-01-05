app.controller('LoginController', function ($scope, $location, ApiService) {

    $scope.login = function () {
        ApiService.login({
            email: $scope.email,
            password: $scope.password
        }).then(function (res) {
            localStorage.setItem('token', res.data.token);
            $location.path('/products');
        }, function () {
            $scope.error = "Invalid login";
        });
    };
});
