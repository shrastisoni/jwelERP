var app = angular.module('JewelleryApp', ['ngRoute']);

app.config(function ($routeProvider, $httpProvider) {

    $routeProvider
        .when('/login', {
            template: `
                <h3>Login</h3>
                <form ng-submit="login()">
                    <input class="form-control mb-2" placeholder="Email" ng-model="email">
                    <input class="form-control mb-2" type="password" placeholder="Password" ng-model="password">
                    <button class="btn btn-success">Login</button>
                </form>
                <p class="text-danger">{{error}}</p>
            `,
            controller: 'LoginController'
        })
        .when('/products', {
            templateUrl:'views/product.html',
            controller: 'ProductController'
        })
        .when('/stock', {
            template: `
                <h3>Stock Ledger</h3>
                <table class="table table-striped">
                    <tr>
                        <th>Product</th>
                        <th>In</th>
                        <th>Out</th>
                        <th>Balance</th>
                    </tr>
                    <tr ng-repeat="s in stock">
                        <td>{{s.product.name}}</td>
                        <td>{{s.in_qty}}</td>
                        <td>{{s.out_qty}}</td>
                        <td>{{s.balance}}</td>
                    </tr>
                </table>
            `,
            controller: 'StockController'
        })
        .when('/sales', {
            templateUrl: 'views/sales.html',
            controller: 'SalesController'
        })
        .when('/purchase', {
            templateUrl: 'views/purchase.html',
            controller: 'PurchaseController'
        })
        .when('/stockledger', {
            templateUrl: 'views/stock-ledger-new.html',
            controller: 'StockLedgerController'
        })
        .when('/profit', {
            templateUrl: 'views/product-profit.html',
            controller: 'ProfitController'
        })
        .when('/openingstock', {
            templateUrl: 'views/opening-stock.html',
            controller: 'OpeningStockController',
            activetab: 'openingstock'
        })
        .when('/dashboard', {
            templateUrl: 'views/dashboard.html',
            controller: 'DashboardController',
            activetab: 'dashboard'
        })
        .when('/category', {
            templateUrl: 'views/category.html',
            controller: 'CategoryController',
            activetab: 'category'
        })
        
        .otherwise({ redirectTo: '/login' });

    // Attach token automatically
    $httpProvider.interceptors.push(function () {
        return {
            request: function (config) {
                let token = localStorage.getItem('token');
                if (token) {
                    config.headers.Authorization = 'Bearer ' + token;
                }
                return config;
            }
        };
    });
});
// app.run(['$rootScope', '$location', function($rootScope, $location){
//     var path = function() { 
//         return $location.path();
//     };
//    $rootScope.$watch(path, function(newVal, oldVal){
//         console.log(newVal);
//         console.log(oldVal);
//         $rootScope.activetab = newVal;
//          console.log($rootScope.activetab);
//    }, true);
// }]);