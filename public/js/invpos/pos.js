(function(){
    var app = angular.module('smartpos', [ ]);

    app.directive('stringToNumber', function() {
        return {
            require: 'ngModel',
            link: function(scope, element, attrs, ngModel) {
              ngModel.$parsers.push(function(value) {
                return '' + value;
              });
              ngModel.$formatters.push(function(value) {
                return parseFloat(value, 10);
              });
            }
        };
    });
    app.controller("SearchItemCtrl", [ '$scope', '$http', '$filter', function($scope, $http, $filter) {
        $scope.items = [ ];

        $http({
            method : 'GET',
            url : '../api/invoiceitem'
        }).then(function (response) {
            $scope.items = response.data;
        }, function (error) {
            console.log(error);
            alert('This is embarassing. An error has occurred. Please check the log for details');
        });

        $scope.invoicetemp = [ ];
        $scope.newinvoicetemp = { };
        $http({
            method : 'GET',
            url : '../api/invoicetemp'
        }).then(function(response){
             $scope.invoicetemp = response.data;
        });

        function getData() {
            $http({
                method : 'GET',
                url : '../api/invoicetemp'
            }).then(function(response){
                $scope.invoicetemp = response.data;
            });
        }

        //Add Sale temp using Barcode Scanner
        $scope.$watch('barcode', function(newValue, oldValue){
            if (newValue) {
                checkBarcode(newValue);
                // $scope.msg = 'Somthing enter';
            }
        });

        function checkBarcode(barcode){
            var item = $filter('filter')($scope.items, {barcode: barcode}, true)[0];
            $http({
                method : 'POST',
                url : '../api/invoicetemp',
                data : { product_id: item.id, cost_per_unit: item.price_per_unit }
            }).then(function(response){
                getData();
            });
        }


        $scope.addSaleTemp = function(item, newinvoicetemp) {
            $http({
                method : 'POST',
                url : '../api/invoicetemp',
                data : { product_id: item.id, cost_per_unit: item.price_per_unit }
            }).then(function(response){
                getData();
            }, function(error){

                console.log(error);
            });    
        }

        $scope.updateSaleTemp = function(newinvoicetemp) {
            $http({
                method : 'PUT',
                url : '../api/invoicetemp/' + newinvoicetemp.id,
                data : { quantity: newinvoicetemp.quantity, price: newinvoicetemp.cost_per_unit * newinvoicetemp.quantity , cost_per_unit : newinvoicetemp.cost_per_unit }
            }).then(function(response){
                getData();
                if (response.data.status == 'Fail') {
                    alert('Stock of this product is currently low');
                }
            });
        }   

        $scope.updateSaleTempDiscount = function(list) {
            var total=0;
            angular.forEach(list , function(newinvoicetemp){
                total+= parseFloat(newinvoicetemp.cost_per_unit * newinvoicetemp.quantity);
            });
            var dpercent = $scope.total_discount/total;

            // alert('You have added a Discount of '+ Math.round(dpercent*100)+ '% to each item sold.');
            angular.forEach(list, function(newinvoicetemp){
                $http({
                    method : 'PUT',
                    url : '../api/invoicetemp/' + newinvoicetemp.id,
                    data : { quantity: newinvoicetemp.quantity, price: newinvoicetemp.cost_per_unit * newinvoicetemp.quantity }
                }).then(function(response){
                    getData();
                });
            });
        }     

        $scope.removeSaleTemp = function(id) {
            $http({
                method : 'DELETE',
                url : '../api/invoicetemp/' + id
            }).then(function(response){
                getData();
            }, function(error){

                alert('error');
            });
        }


        $scope.sum = function(list) {
            var total=0;
            angular.forEach(list , function(newinvoicetemp){
                total+= parseFloat(newinvoicetemp.cost_per_unit * newinvoicetemp.quantity);
            });
            return total;
        }

        // $scope.sumDiscount = function(list){
        //     var t_discount=0;
        //     angular.forEach(list, function(newinvoicetemp){
        //         t_discount+= parseFloat(newinvoicetemp.discount * newinvoicetemp.quantity);
        //     });
        //     return t_discount;
        // }

    }]);

    
})();