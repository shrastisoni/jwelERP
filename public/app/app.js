var app = angular.module('erpApp', ['ngRoute']);
app.config(function($httpProvider) {

    $httpProvider.interceptors.push(function() {
        return {
            request: function(config) {
                var token = localStorage.getItem('token');

                if (token) {
                    config.headers.Authorization = 'Bearer ' + token;
                }
                config.headers.Accept = 'application/json';
                return config;
            }
        };
    });

});
