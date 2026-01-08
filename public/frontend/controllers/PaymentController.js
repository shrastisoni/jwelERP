app.controller('PaymentController', function ($scope, ApiService) {

    $scope.payment = {
        party_id: '',
        amount: '',
        type: 'in',
        mode: 'cash'
    };
    $scope.parties = [];

    ApiService.getCustomers().then(res => {
        $scope.parties = res.data;
    });
    $scope.save = function () {
        ApiService.savePayment($scope.payment)
            .then(() => {
                alert('Payment saved');
                $scope.payment.amount = '';
                load();
            })
            .catch(err => {
                alert(err.data.message || 'Error');
            });
    };

    function load() {
        ApiService.getPayments().then(res => {
            $scope.payments = res.data.data;
        });
    }

    load();
});
