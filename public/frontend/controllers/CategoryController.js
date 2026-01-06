app.controller('CategoryController', function ($scope, ApiService) {

    $scope.category = {};
    $scope.categories = [];
    $scope.editing = false;
    load();

    function load() {
        ApiService.getCategories().then(res => {
            $scope.categories = res.data;
        });
    }

    $scope.save = function () {
        if (!$scope.category.name) {
            alert('Enter category name');
            return;
        }
        if ($scope.editing) {
                ApiService.updateCategory($scope.category.id, $scope.category)
                    .then(() => {
                        reset();
                        load();
                    });
            } else {
                ApiService.addCategory($scope.category).then(() => {
                    $scope.category = {};
                    load();
                }).catch(err => {
                    alert(err.data.message || 'Error');
                });
        }
    };
    $scope.edit = function (c) {
        $scope.category = angular.copy(c);
        $scope.editing = true;
    };

    $scope.delete = function (c) {
        if (!confirm('Delete category?')) return;

        ApiService.deleteCategory(c.id)
            .then(() => load())
            .catch(err => {
                alert(err.data.message);
            });
    };

    function reset() {
        $scope.category = {};
        $scope.editing = false;
    }
});
