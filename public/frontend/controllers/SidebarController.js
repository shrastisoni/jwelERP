
app.controller('SidebarController', function ($scope, $location, AuthService) {

    $scope.isLoggedIn = AuthService.isLoggedIn();

    $scope.isActive = function (path) {
        return $location.path().startsWith(path);
    };

});