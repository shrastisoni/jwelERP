// app.controller('SaleController', function($scope, ApiService) {

//     $scope.sale = {
//         items: [],
//         total_amount: 0
//     };

//     // Load Masters
//     ApiService.get('products').then(res => {
//         $scope.products = res.data;
//     });

//     ApiService.get('parties').then(res => {
//         $scope.parties = res.data;
//     });

//     // Add new item row
//     $scope.addItem = function() {
//         $scope.sale.items.push({
//             weight: 0,
//             rate: 0,
//             amount: 0
//         });
//     };

//     // Remove item
//     $scope.removeItem = function(index) {
//         $scope.sale.items.splice(index, 1);
//         calculateTotal();
//     };

//     // Calculate item amount
//     $scope.calculate = function(item) {
//         item.amount = (item.weight || 0) * (item.rate || 0);
//         calculateTotal();
//     };

//     function calculateTotal() {
//         let total = 0;
//         angular.forEach($scope.sale.items, function(item) {
//             total += item.amount || 0;
//         });
//         $scope.sale.total_amount = total;
//     }

//     // Save Sale
//     $scope.saveSale = function() {

//         ApiService.post('sales', $scope.sale).then(function() {
//             alert('Sale Saved Successfully');

//             $scope.sale = { items: [], total_amount: 0 };
//             $scope.addItem();
//         });

//     };

//     // Init
//     $scope.addItem();
// });
