app.controller('MainController', function ($rootScope, $scope, $location,AuthService) {
    
    $scope.user = AuthService.getUser();
    $scope.isLoggedIn = AuthService.isLoggedIn();

    $scope.isActive = function (path) {
        return $location.path().startsWith(path);
    };
    // console.log($scope.user);
    $scope.logout = function () {
        AuthService.logout();
        $location.path('/login');
        $scope.user = null;
        $scope.isLoggedIn = AuthService.isLoggedIn();
    };
    // $scope.isActive = function (page) {
    //     return $location.path() == page;
    // };
    // $scope.logout = function () {
    //     AuthService.logout();
    //     window.location.href = '#!/login';
    //      $scope.isLogin = AuthService.isLoggedIn();
   
    //     $scope.isLogin = false;
    // };
    // $scope.isLogin = AuthService.isLoggedIn();
    // $scope.logout = function () {
    //     localStorage.removeItem('token');
    //     // window.location.href = '#!/login';
    //     $location.path('/login');
    // };
    // if (!localStorage.getItem('token')) {
    //     // window.location.href = '#!/login';
    //     $location.path('/login');
    // }
});
