app.service('AuthService', function ($window, $http) {
        var baseUrl = 'http://127.0.0.1:8000/api';
    this.setToken = function (token) { 
        $window.localStorage.setItem('auth_token', token);
    };

    this.getToken = function () {
        
        return $window.localStorage.getItem('auth_token');
    };

    this.setUser = function (user) {
        console.log(user);
        var userNew = $window.localStorage.setItem('auth_user', JSON.stringify(user));
        console.log($window.localStorage.getItem('auth_user'))
    };

    this.getUser = function () {
        // console.log('icall');
        // console.log($window.localStorage.getItem('auth_token'));
        // let user = $window.localStorage.getItem('auth_user');
        return JSON.parse($window.localStorage.getItem('auth_user'));
        // return user ? JSON.parse(user) : null;
    };

    this.isLoggedIn = function () {
        return !!this.getToken();
    };

    this.logout = function () {
        $window.localStorage.removeItem('auth_token');
        $window.localStorage.removeItem('auth_user');
    };
    this.getProfile = function () {
        return $http.get(baseUrl + '/profile', auth());
    };

    this.updateProfile = function (data) {
        return $http.put(baseUrl + '/profile', data, auth());
    };

    this.changePassword = function (data) {
        return $http.put(baseUrl + '/change-password', data, auth());
    };

    function auth() {
        return {
            headers: {
                Authorization: 'Bearer ' + $window.localStorage.getItem('auth_token')
            }
        };
    }

});
