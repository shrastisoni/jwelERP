app.factory('ApiService', function ($http) {

    var baseUrl = 'http://127.0.0.1:8000/api';

    return {
        // login: function (data) {
        //     return $http.post(baseUrl + '/login', data);
        // },
        // getProducts: function () {
        //     return $http.get(baseUrl + '/products');
        // },
        // getStock: function () {
        //     return $http.get(baseUrl + '/stock-ledger');
        // }
        login: data => $http.post(baseUrl + '/login', data),
        getProducts: () => $http.get(baseUrl + '/products'),
        getParties: () => $http.get(baseUrl + '/parties'),
        getStock: () => $http.get(baseUrl + '/stock-ledger'),
        saveSale: data => $http.post(baseUrl + '/sales', data),
        savePurchase:data => $http.post(baseUrl + '/purchases', data),
        getCustomers: data => $http.get(baseUrl + '/parties?type=customer'),
        getSuppliers : data => $http.get(baseUrl + '/parties?type=supplier'),
    };
});
