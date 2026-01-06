app.factory('ApiService', function ($http) {

    var baseUrl = 'http://127.0.0.1:8000/api';
    // this.getStockLedger = function () {
    //     return $http.get(baseUrl + '/stock-ledger');
    // };

    // this.getProductLedger = function (productId) {
    //     return $http.get(baseUrl + '/stock-ledger/' + productId);
    // };
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
        getStockLedger: () => $http.get(baseUrl + '/stock-ledger'),
        getProductLedger: data =>$http.get(baseUrl + '/stock-ledger/'+data),
        saveSale: data => $http.post(baseUrl + '/sales', data),
        savePurchase:data => $http.post(baseUrl + '/purchases', data),
        getCustomers: data => $http.get(baseUrl + '/parties?type=customer'),
        getSuppliers : data => $http.get(baseUrl + '/parties?type=supplier'),
        saveProduct  : data => $http.post(baseUrl + '/products', data),
        getCategories: () => $http.get(baseUrl + '/categories'),
        getAllProducts: () => $http.get(baseUrl + '/getallproducts'),
        getProduct :(id) =>$http.get(baseUrl + '/products/'+id),
        updateProduct :(id, data) =>$http.put(baseUrl + '/products/'+id, data),
        deleteProduct :(id)=>$http.delete(baseUrl + '/products/'+id),
        toggleProductStatus :(id)=>$http.patch(baseUrl + '/products/' + id + '/toggle'),
        getProductProfit:() => $http.get(baseUrl + '/reports/product-profit'),
        getPurchaseCostProfit:() => $http.get(baseUrl + '/reports/profit/purchase-cost'),
        addOpeningStock : data => $http.post(baseUrl + '/opening-stock', data),
    };
});
