(function(){
    var app = angular.module('smartpos', [ ]);

    app.controller("SearchItemCtrl", [ '$scope', '$http', '$filter', function($scope, $http, $filter) {
        $scope.items = [ ];

        $http({
            method : 'GET',
            url: '../api/item'
        }).then(function(response){
            $scope.items = response.data;
        });

        $scope.ordertemp = [ ];
        $scope.newordertemp = { };
        
        $http({
            method: 'GET',
            url: '../api/ordertemp'
        }).then(function(response){
            $scope.ordertemp = response.data;
        });


        var destin = '';
        $scope.getDestin = function(destin_id){
            destin = $scope.destin_id;
        }

        $scope.getData = function(){
            $http({
                method: 'GET',
                url: '../api/ordertemp'
            }).then(function (response) {
                $scope.ordertemp = response.data;
            });
        };

        $scope.addOrderTemp = function(item, newordertemp) {
            if (destin === '') {
                Swal.fire({
                    type: 'warning',
                    title: 'No destination...',
                    text: 'Please select a destination Shop OR Store to Add items.'
                });
            }else{
                if (item.in_stock == 0.00) {
                    Swal.fire({
                        type: 'warning',
                        title: 'ZERO Stock...',
                        text: 'The stock of '+item.name+' is currently ZERO. Please Purchase new Stock.'
                    });
                }else{
                    $http({
                        method: 'POST',
                        url: '../api/ordertemp',
                        data: {product_id: item.id, destin_id: destin}
                    }).then(function(response){
                       $scope.getData();
                    }, function(error){

                        });
                }
            }
        }

        $scope.updateOrderTemp = function(newordertemp) {
            $http({
                method: 'PUT',
                url: '../api/ordertemp/' + newordertemp.id ,
                data : { quantity: newordertemp.quantity, destin_unit_cost: newordertemp.destin_unit_cost}
            }).then(function(response){
                if (response.data.status == 'LOW') {
                    Swal.fire({
                        type: 'info',
                        title: 'Low Stock Level...',
                        text: 'Stock of Your Product in your Source Shop/Store is currently less than.'+newordertemp.quantity+'.'
                    });
                } 

                $scope.getData();   
            });
        }   

        $scope.removeOrderTemp = function(id) {
            $http({
                method: 'DELETE',
                url: '../api/ordertemp/' + id
            }).then(function(response){
                $scope.getData();
            });
        }

    }]);
    
})();