app.controller('ProductController', function ($scope, ApiService) {

    $scope.product = {
        name: '',
        category_id: null,
        metal: 'gold',
        purity: '22', 
        weight: 0,
        price: 0
    };
    $scope.products = [];
    $scope.categories = [];
    $scope.showAddSec = true;
    $scope.isEdit = false;
    $scope.showAddSecFun = function(){
        $scope.showAddSec = !$scope.showAddSec;
    };
    // ApiService.getProducts().then(r => $scope.products = r.data);
    ApiService.getCategories().then(res => {
        $scope.categories = res.data;
    });

    $scope.saveProduct = function () {
        ApiService.saveProduct($scope.product)
            .then(res => {
                alert('Product added successfully');
                $scope.showAddSecFun();
                $scope.loadProducts();
                $scope.isEdit = false;
                $scope.product = {
                    name: '',
                    category_id: null,
                    metal: 'gold',
                    weight: 0,
                    purity: '22',   
                    price: 0
                };
                
            })
            .catch(err => {
                alert(
                    err.data?.message ||
                    JSON.stringify(err.data?.errors)
                );
            });
    };


    $scope.loadProducts = function () {
        ApiService.getProducts()
            .then(res => {
                console.log(res.data);
                $scope.products = res.data;
            });
    };

    $scope.loadProducts();
    // const productId = new URLSearchParams(window.location.search).get('id');

    // if (!productId) {
    //     alert('Product ID missing');
    //     return;
    // }
    $scope.getProduct = function (id) {
        ApiService.getProduct(id).then(res => {
            $scope.product = res.data;
            $scope.showAddSecFun();
            $scope.isEdit = true;
        });

    };
    

    $scope.updateProduct = function (id) {
        ApiService.updateProduct(id, $scope.product)
            .then(() => {
                alert('Product updated successfully');
                // window.location.href = 'product-list.html';
                $scope.showAddSecFun();
                $scope.loadProducts();
                $scope.isEdit = false;
                $scope.product = {
                    name: '',
                    category_id: null,
                    metal: 'gold',
                    weight: 0,
                    purity: '22',   
                    price: 0
                };
                // $scope.showAddSecFun();
                // $scope.loadProducts();
            })
            .catch(err => {
                alert(
                    err.data?.message ||
                    JSON.stringify(err.data?.errors)
                );
            });
    };
    $scope.deleteProduct = function (id) {

        if (!confirm('Are you sure you want to delete this product?')) {
            return;
        }

        ApiService.deleteProduct(id)
            .then(res => {
                alert(res.data.message);
                $scope.loadProducts();
            })
            .catch(err => {
                alert(err.data.message);
            });
    };

});
