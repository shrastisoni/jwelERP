app.factory('AuthService', function($http) {

    var BASE_URL = 'http://127.0.0.1:8000/api/';

    return {

        login: function(data) {
            return $http.post(BASE_URL + 'login', data);
        },

        logout: function() {
            localStorage.removeItem('token');
        }

    };
});
