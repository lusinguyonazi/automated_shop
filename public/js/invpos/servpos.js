(function(){
    var app = angular.module('smartpos', [ ]);

    app.controller("SearchItemCtrl", [ '$scope', '$http', function($scope, $http) {
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
                data : { repeatition: newservinvoicetemp.repeatition, discount: newservinvoicetemp.discount / newservinvoicetemp.repeatition , cost_per_unit : newservinvoicetemp.cost_per_unit }
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
        $scope.sum = function(list) {
            var total=0;
            angular.forEach(list , function(newservinvoicetemp){
                total+= parseFloat(newservinvoicetemp.cost_per_unit * newservinvoicetemp.repeatition);
            });
            return total;
        }

        $scope.sumDiscount = function(list){
            var t_discount=0;
            angular.forEach(list, function(newservinvoicetemp){
                t_discount+= parseFloat(newservinvoicetemp.discount * newservinvoicetemp.repeatition);
            });
            return t_discount;
        }

    }]);
})();