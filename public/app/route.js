// app.config(function($routeProvider) {
//     $routeProvider
//       .when('/login', { templateUrl:'views/login.html', controller:'LoginCtrl' })
//       .when('/sale', { templateUrl:'views/sale.html', controller:'SaleCtrl' })
//       .otherwise({ redirectTo:'/login' });
// });
app.config(function($routeProvider) {

    $routeProvider
        .when('/login', {
            templateUrl: 'views/login.html',
            controller: 'LoginController'
        })

        .when('/dashboard', {
            templateUrl: 'views/dashboard.html'
        })

        .when('/sales', {
            templateUrl: 'views/sale.html',
            controller: 'SaleController'
        })

        .when('/ledger', {
            templateUrl: 'views/stock-ledger.html',
            controller: 'LedgerController'
        })

        .otherwise({
            redirectTo: '/login'
        });

});
