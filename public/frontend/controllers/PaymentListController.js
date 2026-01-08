app.controller('PaymentListController', function ($scope, ApiService) {

    $scope.payments = [];
    $scope.search = '';
    $scope.type = '';

    $scope.editPayment = null;

    $scope.load = function () {
        ApiService.getPayments({
            search: $scope.search,
            type: $scope.type
        }).then(res => {
            $scope.payments = res.data.data;
        });
    };

    $scope.openEdit = function (p) {
        $scope.editPayment = angular.copy(p);
    };

    $scope.update = function () {
        ApiService.updatePayment(
            $scope.editPayment.id,
            $scope.editPayment
        ).then(() => {
            alert('Payment updated');
            $scope.editPayment = null;
            $scope.load();
        });
    };

    $scope.delete = function (id) {
        if (!confirm('Delete this payment?')) return;

        ApiService.deletePayment(id).then(() => {
            alert('Deleted');
            $scope.load();
        });
    };

    $scope.load();
});
