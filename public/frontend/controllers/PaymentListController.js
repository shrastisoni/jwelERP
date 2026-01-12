app.controller('PaymentListController', function ($scope, ApiService) {

    $scope.payments = [];
    $scope.search = '';
    $scope.type = '';

    $scope.editPayment = null;

    $scope.load = function () {
        console.log($scope.search);
        console.log($scope.type);
        ApiService.getPayments({
            search: $scope.search,
            type: $scope.type
        }).then(res => {
            $scope.payments = res.data.data;
            console.log($scope.payments);
        });
    };
 
    // $scope.openEdit = function (p) {
    //     $scope.editPayment = angular.copy(p);
    // };
$scope.openEdit = function (p) {
    $scope.editPayment = angular.copy(p);

    const modal = new bootstrap.Modal(
        document.getElementById('paymentModal')
    );
    modal.show();
};
    // $scope.update = function () {
    //     ApiService.updatePayment(
    //         $scope.editPayment.id,
    //         $scope.editPayment
    //     ).then(() => {
    //         alert('Payment updated');
    //         $scope.editPayment = null;
    //         $scope.load();
    //     });
    // };
$scope.update = function () {
    ApiService.updatePayment(
        $scope.editPayment.id,
        $scope.editPayment
    ).then(() => {

        const modalEl = document.getElementById('paymentModal');
        bootstrap.Modal.getInstance(modalEl).hide();

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
    $scope.$watchGroup(['search', 'type'], function () {
        $scope.load();
        console.log("here");
    });


    $scope.load(); 
});
