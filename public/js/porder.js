(function(){
    var app = angular.module('smartpos', [ ]);

    app.controller("SearchItemCtrl", [ '$scope', '$http', '$filter', '$timeout', function($scope, $http, $filter, $timeout) {
        $scope.items = [ ];

        $http({
            method : 'GET',
            url : '/api/item'
        }).then(function(response){
            $scope.items = response.data;
        }, function(error) {
            alert('yes');
        });


        $scope.pordertemp = [ ];
        $scope.newpordertemp = { };

        $http({
            method : 'GET',
            url : 'api/pordertemp'
        }).then(function(response){
            $scope.pordertemp = response.data;
        });

        $http({
            method : 'GET',
            url : 'api/usebarcode'
        }).then(function(response){
            $scope.usebarcode = response.data.usebarcode;
            if ($scope.usebarcode) {
                // Kick off the interval
                $scope.intervalFunction();
            }
        });

        $scope.getData = function(){
            $http({
                method : 'GET',
                url : 'api/pordertemp'
            }).then(function(response){
                $scope.pordertemp = response.data;
            });
        };

        // Function to replicate setInterval using $timeout service.
        $scope.intervalFunction = function(){
            $timeout(function() {
              $scope.getData();
              $scope.intervalFunction();
            }, 15000)
        };
        
        $scope.addOrderTemp = function(item, newpordertemp) {
            $http({
                method : 'POST',
                url : 'api/pordertemp',
                data: {product_id: item.id, quantity_in: 0 }
            }).then(function(response){
                $scope.getData();
            });
        }

        $scope.updateOrderTemp = function(newpordertemp) {
            $http({
                method : 'PUT',
                url : 'api/pordertemp/' + newpordertemp.id,
                data: {qty: newpordertemp.qty, unit_cost: newpordertemp.unit_cost}
            }).then( function(response){
                if(response.data.status == 'WRONG'){
                    Swal.fire({
                        type: 'info',
                        title: 'WRONG QTY...',
                        text: response.data.msg
                    });
                }else if(response.data.status == 'FAIL'){
                    Swal.fire({
                        type: 'info',
                        title: 'INVALID EXPIRE DATE.',
                        text: 'You have entered invalid expire date. Please enter a valid expire date for ' +newpordertemp.product.name+ "'s porder entry using the format in the text field."
                    });
                }

                $scope.getData();
            });
        }   

        $scope.removeOrderTemp = function(id) {
            $http({
                method : 'DELETE',
                url : 'api/pordertemp/' + id
            }).then(function(response){
                $scope.getData();
            });
        }


        $scope.sum = function(list) {
            var total=0;
            angular.forEach(list , function(newpordertemp){
                total+= parseFloat(newpordertemp.qty*newpordertemp.unit_cost);
            });
            return total;
        }
    }]);

})();