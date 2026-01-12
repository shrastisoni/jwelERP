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
        // getParties: () => $http.get(baseUrl + '/parties'),
        getStock: () => $http.get(baseUrl + '/stock-ledger'),
        getStockLedger: () => $http.get(baseUrl + '/stock-ledger'),
        getProductLedger: data =>$http.get(baseUrl + '/stock-ledger/'+data),
        saveSale: data => $http.post(baseUrl + '/sales', data),
        getSales :(params) => $http.get(baseUrl + '/sales' , { params: params }),
        getSale  :(id) =>$http.get(baseUrl + '/sales/'+id),
        
        savePurchase:data => $http.post(baseUrl + '/purchases', data),
        // getPurchases: () => $http.get(baseUrl + '/purchases'),
        getPurchases :(params) => $http.get(baseUrl + '/purchases' , { params: params }),
        getPurchase  :(id) =>$http.get(baseUrl + '/purchases/'+id),
        getCustomers: data => $http.get(baseUrl + '/parties?type=customer'),
        getSuppliers : data => $http.get(baseUrl + '/parties?type=supplier'),
        saveProduct  : data => $http.post(baseUrl + '/products', data),
        getCategories: () => $http.get(baseUrl + '/categories'),
        getAllProducts: () => $http.get(baseUrl + '/getallproducts'),
        getProduct :(id) =>$http.get(baseUrl + '/products/'+id),
        getProductsByCategory :(id) =>$http.get(baseUrl + '/products/category/'+id),
        updateProduct :(id, data) =>$http.put(baseUrl + '/products/'+id, data),
        deleteProduct :(id)=>$http.delete(baseUrl + '/products/'+id),
        toggleProductStatus :(id)=>$http.patch(baseUrl + '/products/' + id + '/toggle'),
        getProductProfit:() => $http.get(baseUrl + '/reports/product-profit'),
        getCategoryStock:() => $http.get(baseUrl + '/reports/category-stock'),
        getPurchaseCostProfit:() => $http.get(baseUrl + '/reports/profit/purchase-cost'),
        addOpeningStock : data => $http.post(baseUrl + '/opening-stock', data),
        getDashboard:() => $http.get(baseUrl + '/dashboard'),
        getDashboardCharts:() => $http.get(baseUrl + '/dashboard/charts'),
        addCategory : data => $http.post(baseUrl + '/categories', data),
        updateCategory:(id, data) =>$http.put(baseUrl + '/categories/'+id, data),
        deleteCategory  :(id)=>$http.delete(baseUrl + '/categories/'+id),
        
        getCustomers :(q = '') => $http.get(baseUrl + '/customers?q=' + q),
        addCustomer : data => $http.post(baseUrl + '/customers', data),
        updateCustomer:(id, data) =>$http.put(baseUrl + '/customers/'+id, data),
        deleteCustomer  :(id)=>$http.delete(baseUrl + '/customers/'+id),
        getCustomerLedger:(id) => $http.get(baseUrl + '/customers/' + id + '/ledger'),
        
        getPayments :(data) => $http.get(baseUrl + '/payments', data),
        savePayment: data => $http.post(baseUrl + '/payments', data),
        updatePayment :(id, data) =>$http.put(baseUrl + '/payments/'+id, data),
        deletePayment  :(id)=>$http.delete(baseUrl + '/payments/'+id),
        
        getParties :(params) => $http.get(baseUrl + '/parties' , { params: params }),
        saveParty : data => $http.post(baseUrl + '/parties', data),
        updateParty:(id, data) =>$http.put(baseUrl + '/parties/'+id, data),
        deleteParty  :(id)=>$http.delete(baseUrl + '/parties/'+id),

        // getLowStock:() => $http.get(baseUrl + '/dashboard'),
        getRecentSales:() => $http.get(baseUrl + '/dashboard/recent-sales'),
        getLowStock:() => $http.get(baseUrl + '/dashboard/low-stock'),
        
       
    };
});
