app.controller('PartyController', function ($scope, ApiService, $timeout) {

    $scope.parties = [];
    $scope.search = '';
    $scope.filterType = '';
    var searchTimeout = "";
    $scope.form = {};
    $scope.editing = false;

$scope.openAdd = function () {
    $scope.form = {};
    $scope.editing = false;
    openModal();
};

$scope.openEdit = function (party) {
    $scope.form = angular.copy(party);
    $scope.editing = true;
    openModal();
};

function openModal() {
    const modal = new bootstrap.Modal(
        document.getElementById('partyModal')
    );
    modal.show();
}

function closeModal() {
    const modalEl = document.getElementById('partyModal');
    const modal = bootstrap.Modal.getInstance(modalEl);
    modal.hide();
}
    $scope.load = function () {
        console.log($scope.search);
        console.log($scope.filterType);
        ApiService.getParties({
            search: $scope.search,
            type: $scope.filterType
        }).then(res => {
            $scope.parties = res.data;
        });
    };
     $scope.onSearchChange = function () {
        if (searchTimeout) $timeout.cancel(searchTimeout);

        searchTimeout = $timeout(() => {
            $scope.load();
        }, 100);
    };
    // $scope.save = function () {

    //     let req = $scope.editing
    //         ? ApiService.updateParty($scope.form.id, $scope.form)
    //         : ApiService.saveParty($scope.form);

    //     req.then(() => {
    //         $scope.form = {};
    //         $scope.editing = false;
    //         $scope.load();
    //     }).catch(err => alert(err.data.message));
    // };

// MODIFY SAVE FUNCTION
$scope.save = function () {

    if ($scope.editing) {
        ApiService.updateParty($scope.form.id, $scope.form)
            .then(() => {
                closeModal();
                $scope.load();
            });
    } else {
        ApiService.saveParty($scope.form)
            .then(() => {
                closeModal();
                $scope.load();
            });
    }
};
    $scope.edit = function (p) {
        $scope.form = angular.copy(p);
        $scope.editing = true;
    };

    $scope.delete = function (id) {
        if (!confirm('Delete party?')) return;

            ApiService.deleteParty(id)
            .then($scope.load)
            .catch(err => alert(err.data.message));
    };

    $scope.load();
});
