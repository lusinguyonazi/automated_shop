(function(){
    var app = angular.module('smartpos', [ ]);

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


        $scope.servitems = [ ];

        $http({
            method : 'GET',
            url : '../api/servitem'
        }).then(function (response){
            $scope.servitems = response.data;
        });

        $scope.servinvoicetemp = [ ];
        $scope.newservinvoicetemp = { };
        $http({
            method : 'GET',
            url : '../api/servinvoicetemp'
        }).then(function (response){
            $scope.servinvoicetemp = response.data;
        });

        function getSevData() {
            $http({
            method : 'GET',
            url : '../api/servinvoicetemp'
        }).then(function (response){
            $scope.servinvoicetemp = response.data;
        });
        }

        $scope.addServiceSaleTemp = function(item, newservinvoicetemp) {
            $http({
                method :'POST',
                url : '../api/servinvoicetemp',
                data : { service_id: item.id, cost_per_unit: item.price }
            }).then(function(response){
                getSevData();
            });   
        }

        $scope.updateServiceSaleTemp = function(newservinvoicetemp) {
            $http({
                method : 'PUT',
                url : '../api/servinvoicetemp/' + newservinvoicetemp.id,
                data : { repeatition: newservinvoicetemp.repeatition, discount: newservinvoicetemp.discount / newservinvoicetemp.repeatition , cost_per_unit:newservinvoicetemp.cost_per_unit }
            }).then(function(response){
                getSevData();
            });
        }    

        $scope.updateSaleTempServiceDiscount = function(list) {
            var total=0;
            angular.forEach(list , function(newservinvoicetemp){
                total+= parseFloat(newservinvoicetemp.cost_per_unit * newservinvoicetemp.repeatition);
            });
            var dpercent = $scope.total_discount/total;

            angular.forEach(list, function(newservinvoicetemp){
                $http({
                    method : 'PUT',
                    url : '../api/servinvoicetemp/' + newservinvoicetemp.id,
                    data:  { repeatition: newservinvoicetemp.repeatition, discount: newservinvoicetemp.cost_per_unit*dpercent }

                }).then(function(response){
                    getSevData();
                });

            });
        }

        $scope.removeServiceSaleTemp = function(id) {
            $http({
                method : 'DELETE',
                url :'../api/servinvoicetemp/' + id
            }).then(function(response){
                getSevData();
            });
        }

        $scope.sumService = function(list) {
            var total=0;
            angular.forEach(list , function(newservinvoicetemp){
                total+= parseFloat(newservinvoicetemp.cost_per_unit * newservinvoicetemp.repeatition);
            });
            return total;
        }

        // $scope.sumServiceDiscount = function(list){
        //     var t_discount=0;
        //     angular.forEach(list, function(newservinvoicetemp){
        //         t_discount+= parseFloat(newservinvoicetemp.discount * newservinvoicetemp.repeatition);
        //     });
        //     return t_discount;
        // }

    }]);

    
})();