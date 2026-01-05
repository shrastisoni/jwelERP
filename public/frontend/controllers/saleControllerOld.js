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
app.controller('SaleController', function ($scope, ApiService) {
    
    $scope.sale = {
        party_id: null,
        invoice_date: new Date(),
        items: []
    };
    $scope.parties = [];
    $scope.products = [];
    ApiService.getProducts().then(res => $scope.products = res.data);
    ApiService.getParties().then(res => $scope.parties = res.data);
    // ApiService.getCustomers().then(r => $scope.parties = r.data);
    $scope.addItem = function () {
        $scope.sale.items.push({
            // product_id: '',
            // weight: 0,
            // rate: 0,
            // amount: 0
            product_id: null,
            quantity: 1,
            weight: 0,
            rate: 0,
            amount: 0
        });
    };

    $scope.removeItem = function (index) {
        $scope.sale.items.splice(index, 1);
        $scope.calculateTotal();
    };

    $scope.calc = function (item) {
        item.amount = item.weight * item.rate;
        $scope.calculateTotal();
    };

    $scope.totalAmount = function () {
        var t = 0;
        angular.forEach($scope.sale.items, function (i) {
            t += (i.weight * i.rate) || 0;
        });
        return t;
    };

    $scope.calculateTotal = function () {
        let total = 0;
        angular.forEach($scope.sale.items, function (item) {
            total += item.amount;
        });
        $scope.sale.total_amount = total;
    };

    $scope.save = function () {
        if (!$scope.sale.party_id) {
            alert('Please select a party');
            return;
        }

        if ($scope.sale.items.length === 0) {
            alert('Add at least one item');
            return;
        }
        $scope.saveSale = function () {
            console.log("I called");
            ApiService.saveSale($scope.sale)
                .then(() => alert('Sale Saved'))
                .catch(e => alert(e.data.message || 'Insufficient Stock'));
        };
        // ApiService.saveSale($scope.sale).then(function () {
        //     alert('Sale saved successfully');
        //     $scope.sale = { items: [] };
        //     $scope.addItem();
        // });
        // ApiService.saveSale($scope.sale).then(function () {
        //     $scope.message = "Sale saved successfully";
        //     $scope.sale = { items: [] };
        // });
    };

    $scope.addItem();
});
