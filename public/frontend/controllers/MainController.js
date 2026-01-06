app.controller('MainController', function ($scope, $location) {

    $scope.isActive = function (page) {
        console.log(window.location.pathname.includes(page));
        return window.location.pathname.includes(page);
    };

    $scope.logout = function () {
        localStorage.removeItem('token');
        window.location.href = '#!/login';
    };
    if (!localStorage.getItem('token')) {
        window.location.href = '#!/login';
    }
});
