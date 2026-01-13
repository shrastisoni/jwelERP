var app = angular.module('JewelleryApp', ['ngRoute']);

app.config(function ($routeProvider, $httpProvider) {

    $routeProvider
        // .when('/login', {
        //     template: `
        //         <h3>Login</h3>
        //         <form ng-submit="login()">
        //             <input class="form-control mb-2" placeholder="Email" ng-model="email">
        //             <input class="form-control mb-2" type="password" placeholder="Password" ng-model="password">
        //             <button class="btn btn-success">Login</button>
        //         </form>
        //         <p class="text-danger">{{error}}</p>
        //     `,
        //     controller: 'LoginController'
        // })
         .when('/login', {
            templateUrl: 'views/login.html',
            controller: 'LoginController',
            public: true
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
        .when('/sale', {
            templateUrl: 'views/sales.html',
            controller: 'SalesController'
        })
        .when('/sales', {
            templateUrl: 'views/sales-list.html',
            controller: 'SalesListController'
        })
        .when('/sale/view/:id', {
            templateUrl: 'views/sale-view.html',
            controller: 'SaleViewController'
        })

        .when('/purchase', {
            templateUrl: 'views/purchase.html',
            controller: 'PurchaseController'
        })
        .when('/purchases', {
            templateUrl: 'views/purchase-list.html',
            controller: 'PurchaseListController'
        })
        .when('/purchase/view/:id', {
            templateUrl: 'views/purchase-view.html',
            controller: 'PurchaseViewController'
        })
        .when('/stockledger', {
            templateUrl: 'views/stock-ledger-new.html',
            controller: 'StockLedgerController'
        })
        .when('/profit', {
            templateUrl: 'views/product-profit.html',
            controller: 'ProfitController'
        })
        .when('/profit-report', {
            templateUrl: 'views/profit.html',
            controller: 'ProfitController',
            title: 'Profit Report'
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
        .when('/productbycategory', {
            templateUrl: 'views/category-products.html',
            controller: 'CategoryProductController',
            activetab: 'productbycategory'
        })
        .when('/categorystock', {
            templateUrl: 'views/category-stock.html',
            controller: 'CategoryStockController',
            activetab: 'categorystock'
        })
        .when('/category', {
            templateUrl: 'views/category.html',
            controller: 'CategoryController',
            activetab: 'category'
        })
        .when('/customers', {
            templateUrl: 'views/customers.html',
            controller: 'CustomerController',
            activetab: 'customers'
        })
        // .when('/customerledger', {
        //     templateUrl: 'views/customer-ledger.html',
        //     controller: 'CustomerLedgerController',
        //     activetab: 'customerledger'
        // })
        .when('/customers/:customerId/ledger', {
            templateUrl: 'views/customer-ledger.html',
            controller: 'CustomerLedgerController'
        })
        .when('/payments', {
            templateUrl: 'views/payment.html',
            controller: 'PaymentController'
        })
        .when('/payment-list', {
            templateUrl: 'views/payment-list.html',
            controller: 'PaymentListController'
        })
        .when('/parties', {
            templateUrl: 'views/parties.html',
            controller: 'PartyController'
        })
        .when('/party/ledger/:id', {
            templateUrl: 'views/party-ledger.html',
            controller: 'PartyLedgerController'
        })
        .when('/stock-valuation', {
            templateUrl: 'views/stock-valuation.html',
            controller: 'StockValuationController'
        })
        .when('/category-valuation', {
            templateUrl: 'views/category-valuation.html',
            controller: 'CategoryValuationController'
        })
        .when('/profile', {
            templateUrl: 'views/profile.html',
            controller: 'ProfileEditController'
        })
        .when('/change-password', {
            templateUrl: 'views/change-password.html',
            controller: 'ChangePasswordController'
        })

        .otherwise({ redirectTo: '/login' });

    // Attach token automatically
    // $httpProvider.interceptors.push(function () {
    //     return {
    //         request: function (config) {
    //             let token = localStorage.getItem('token');
    //             if (token) {
    //                 config.headers.Authorization = 'Bearer ' + token;
    //             }
    //             return config;
    //         },
    //         responseError: function (response) {
    //             if (response.status === 401) {
    //                 AuthService.logout();
    //                 $location.path('/login');
    //             }
    //             return $q.reject(response);
    //         }
    //     };
    // });
    $httpProvider.interceptors.push([
        '$q', '$location', '$injector',
        function ($q, $location, $injector) {

            return {
                request: function (config) {
                    var AuthService = $injector.get('AuthService');
                    var token = AuthService.getToken();

                    if (token) {
                        config.headers.Authorization = 'Bearer ' + token;
                    }
                    return config;
                },

                responseError: function (response) {
                    if (response.status === 401) {
                        var AuthService = $injector.get('AuthService');
                        AuthService.logout();
                        $location.path('/login');
                    }
                    return $q.reject(response);
                }
            };
        }
    ]);
});
app.run(function ($rootScope, $location, AuthService) {
    $rootScope.pageTitle = '';
    $rootScope.$on('$routeChangeStart', function (event, next) {

        if (!next.public && !AuthService.isLoggedIn()) {
            event.preventDefault();
            $location.path('/login');
            $rootScope.sidebarCollapsed =
            localStorage.getItem('sidebarCollapsed') === 'true';
        }
    });
    $rootScope.$on('$routeChangeSuccess', function (event, current) {
        if (current.$$route && current.$$route.title) {
            $rootScope.pageTitle = current.$$route.title;
        }
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