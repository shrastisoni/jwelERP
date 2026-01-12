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

app.controller('CategoryProductController', function ($scope, ApiService) {

    $scope.categories = [];
    $scope.products = [];
    $scope.selectedCategory = null;

    // load categories
    ApiService.getCategories().then(res => {
        $scope.categories = res.data;
    });

    $scope.loadProducts = function () {
        if (!$scope.selectedCategory) {
            $scope.products = [];
            return;
        }

        ApiService.getProductsByCategory($scope.selectedCategory)
            .then(res => {
                $scope.products = res.data;
            });
    };
});
app.controller('CategoryStockController', function ($scope, ApiService) {

    $scope.rows = [];

    ApiService.getCategoryStock().then(res => {
        $scope.rows = res.data;
    });

});

app.controller('CategoryValuationController', function ($scope, ApiService) {

    $scope.rows = [];
    $scope.grandTotal = 0;

    ApiService.getCategoryValuation().then(res => {
        $scope.rows = res.data;

        $scope.grandTotal = $scope.rows.reduce((sum, r) => {
            return sum + parseFloat(r.total_value || 0);
        }, 0);
    });

});

