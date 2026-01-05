app.factory('ApiService', function($http) {

    var BASE_URL = 'http://127.0.0.1:8000/api/';

    return {

        get: function(url) {
            return $http.get(BASE_URL + url);
        },

        post: function(url, data) {
            return $http.post(BASE_URL + url, data);
        }

    };
    // Added after produuct module
     this.getSuppliers = function () {
        return $http.get(BASE_URL + '/parties?type=supplier');
    };

    this.getProducts = function () {
        return $http.get(BASE_URL + '/products');
    };
    this.savePurchase = function (data) {
        return $http.post(BASE_URL + '/purchases', data);
    };
});
