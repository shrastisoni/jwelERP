app.controller('LoginController', function ($scope, $location, ApiService, AuthService) {
    // $scope.user = {};
    $scope.login = function () {

        ApiService.login($scope.user).then(function (res) {
            $location.path('/dashboard');
            AuthService.setToken(res.data.token);
            AuthService.setUser(res.data.user);
 
        }, function () {
            alert('Invalid credentials');
        });
    };

});
