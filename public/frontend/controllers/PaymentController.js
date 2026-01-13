app.controller('PaymentController', function ($scope, ApiService, $location,$rootScope) {
    $rootScope.pageTitle = 'Payments';
    $scope.payment = {
        party_id: '',
        amount: '',
        type: 'in',
        mode: 'cash'
    };
    $scope.parties = [];

    ApiService.getParties().then(res => {
        $scope.parties = res.data;
    });

    $scope.save = function () {
        ApiService.savePayment($scope.payment)
            .then(() => {
                alert('Payment saved');
                $scope.payment.amount = '';
                load();
                $location.path('/payment-list')
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

    // AUTO SELECT RECEIVE / PAY BASED ON PARTY TYPE
$scope.$watch('payment.party_id', function (partyId) {

    if (!partyId || !$scope.parties) return;

    const party = $scope.parties.find(p => p.id == partyId);

    if (!party) return;

    // Munim logic
    if (party.type === 'customer') {
        $scope.payment.type = 'in';   // Receive
    }

    if (party.type === 'supplier') {
        $scope.payment.type = 'out';  // Pay
    }

});

});
