app.controller('LoginController', function($scope, AuthService, $location) {

    $scope.user = {};

    $scope.login = function() {

        AuthService.login($scope.user).then(function(response) {

            localStorage.setItem('token', response.data.token);
            $location.path('/dashboard');

        }, function() {
            alert('Invalid Login');
        });

    };

});
