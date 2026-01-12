app.controller('ChangePasswordController', function ($scope, AuthService) {

    $scope.form = {};

    $scope.save = function () {
        AuthService.changePassword($scope.form).then(() => {
            alert('Password changed');
            $scope.form = {};
        }).catch(err => {
            alert(err.data.message);
        });
    };
});
