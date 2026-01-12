app.controller('ProfileEditController', function ($scope, AuthService) {

    $scope.user = {};

    AuthService.getProfile().then(res => {
        $scope.user = res.data; 
    });

    $scope.save = function () {
        AuthService.updateProfile($scope.user).then(res => {
            alert('Profile updated');

            // Update localStorage user
            localStorage.setItem('user', JSON.stringify(res.data.user));
        });
    };
});
