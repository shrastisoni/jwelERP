app.controller('MainController', function ($rootScope, $scope, $location) {
    
    $scope.isActive = function (page) {
        return $location.path() == page;
    };

    $scope.logout = function () {
        localStorage.removeItem('token');
        // window.location.href = '#!/login';
        $location.path('/login');
    };
    if (!localStorage.getItem('token')) {
        // window.location.href = '#!/login';
        $location.path('/login');
    }
});
