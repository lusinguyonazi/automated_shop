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
		$scope.mros = [ ];
		$scope.pms = [ ];
		$scope.rms = [ ];
		$scope.products = [ ];
		$scope.product_made = [ ]; 
        $scope.falsetrue = false;

		$http({
            method: 'GET',
            url: 'api/prod-items'
        }).then(function(response){
            $scope.mros = response.data.mros;
			$scope.pms = response.data.pms;
			$scope.rms = response.data.rms;
			$scope.products = response.data.products;
			$scope.product_made =response.data.product_made; 
        });



// *********** RAW MATERIALS *******************//
        $scope.rmusedtemps = [ ];
        $http({
            method: 'GET',
            url : 'api/rmusedtemp'
        }).then(function(response){
            $scope.rmusedtemps = response.data;

        });

        function getRM(){
        	$http({
            method: 'GET',
            url : 'api/rmusedtemp'
        	}).then(function(response){
           		 $scope.rmusedtemps = response.data;
        	});
        }
       
        $scope.addRM = function(){

        	 $http({
                method: 'POST',
                url : 'api/prod-items',
                data: { rm_id: $scope.rm_id, for : 'rm' }
            }).then(function(response){
                $scope.rm_id = '';
            	getRM();
                recalculate();
                getProductMade();
            });
        }

        $scope.updateRMTemp = function(newrmusedtemp) {
            $http({
                method: 'PUT',
                url : 'api/rmusedtemp/' + newrmusedtemp.id,
                data: { quantity: newrmusedtemp.quantity, unit_cost: newrmusedtemp.unit_cost, total: newrmusedtemp.total}
            }).then(function(response){

                if (response.data.status == 'LOW') {
                    Swal.fire({
                        type: 'info',
                        title: 'LOW STOCK...',
                        text: response.data.msg
                    });
                }else {
                     if(response.data.status == 'WRONG'){
                        Swal.fire({
                            type: 'info',
                            title: 'WRONG QTY...',
                            text: response.data.msg
                        });
                     }
                }

               getRM();
               recalculate();
               getProductMade();
            });
        }   

        $scope.removeRMTemp = function(id) {
            $http({
                method: 'DELETE',
                url : 'api/rmusedtemp/' + id
            }).then(function(response){
                getRM();
                recalculate();
                getProductMade();
            });
        }

        $scope.sumRM = function(list) {
            var total=0;
            angular.forEach(list , function(rmusedtemps){
                total+= parseFloat(rmusedtemps.total);
            });
            return total;
        }

// *********** PACKING MATERIALS ************//
		$scope.pmusedtemps = [ ];
        $http({
            method: 'GET',
            url : 'api/pmusedtemp'
        }).then(function(response){
            $scope.pmusedtemps = response.data;

             if ($scope.pmusedtemps.length > 0) {
                $scope.falsetrue = true;
             }else{
                $scope.falsetrue = false;
             }
        });

        function getPM(){
        	$http({
            method: 'GET',
            url : 'api/pmusedtemp'
        	}).then(function(response){
           		 $scope.pmusedtemps = response.data;
        	});
        }

        $scope.addPM = function(pm){
        	$http({
                method: 'POST',
                url : 'api/prod-items',
                data: { pm_id: $scope.pm_id, for : 'pm' }
            }).then(function(response){
                $scope.pm_id;
            	getPM();
                recalculate();
                getProductMade();
            });
        }

         $scope.updatePMTemp = function(newpmusedtemp) {
            $http({
                method: 'PUT',
                url: 'api/pmusedtemp/'+ newpmusedtemp.id,
                data: {quantity: newpmusedtemp.quantity, unit_packed: newpmusedtemp.unit_packed, product_packed: newpmusedtemp.product_packed, unit_cost: newpmusedtemp.unit_cost, total: newpmusedtemp.total}
            }).then(function(response){

                if (response.data.status == 'LOW') {
                    Swal.fire({
                        type: 'info',
                        title: 'LOW STOCK...',
                        text: data.msg
                    });
                }else {
                     if(response.data.status == 'WRONG'){
                        Swal.fire({
                            type: 'info',
                            title: 'WRONG QTY...',
                            text: data.msg
                        });
                     }
                }

                getPM();
                recalculate();
                getProductMade();
            });
        }   

        $scope.removePMTemp = function(id) {
            $http({
                method: 'DELETE',
                url : 'api/pmusedtemp/' + id
            }).then(function(response) {
                getPM();
                recalculate();
                getProductMade();
            });
        }

        $scope.sumPM = function(list) {
            var total=0;
            angular.forEach(list , function(pmusedtemps){
                total+= parseFloat(pmusedtemps.total);
            });
            return total;
        }

//***************** MROS **********************//
		$scope.mrousedtemps = [ ];

        $http({
            method: 'GET',
            url : 'api/mrousedtemp'
        }).then(function(response){
            $scope.mrousedtemps = response.data;
        });

        function getMro(){
        	$http({
            method: 'GET',
            url : 'api/mrousedtemp'
        	}).then(function(response){
           		 $scope.mrousedtemps = response.data;
        	});
        }

        $scope.addMro = function(mro){
        	$http({
                method: 'POST',
                url : 'api/prod-items',
                data: {mro_id: $scope.mro_id, for : 'mro' }
            }).then(function(response){
                $scope.mro_id = '';
               
            	getMro();
                recalculate();
                getProductMade();
            });
        }

        $scope.updateMroTemp = function(newmrousedtemp) {
            $http({
                method : 'PUT',
                url : 'api/mrousedtemp/' + newmrousedtemp.id,
                data : { quantity: newmrousedtemp.quantity, unit_cost: newmrousedtemp.unit_cost, total: newmrousedtemp.total} 
            }).then(function(response){
                if(response.data.status == 'WRONG'){
                    Swal.fire({
                        type: 'info',
                        title: 'WRONG QTY...',
                        text: response.data.msg
                    });
                }
                getMro();
                recalculate();
                getProductMade();
            });
        }   

        $scope.removeMroTemp = function(id) {
            $http({
                method: 'DELETE',
                url: 'api/mrousedtemp/' + id
            }).then(function(response){
                
                getMro();
                recalculate();
                getProductMade();
            });
        }

         $scope.sumMro = function(list) {
            var total=0;
            angular.forEach(list , function(mrousedtemps){
                total+= parseFloat(mrousedtemps.total);
            });
            return total;
        }

    //***************** PRODUCTS ******************//
        function getProductMade(){
                $http({
                method: 'GET',
                url : 'api/product-made'
                }).then(function(response){
                    $scope.product_made = response.data.product_made;

                 if ($scope.pmusedtemps.length > 0) {
                    $scope.falsetrue = true;
                  }else{
                    $scope.falsetrue = false;
                  }
                }, function(error){

                });
            }

    	$scope.AddProducts = function(product_made){
    		$http({
    			method : 'POST',
    			url: 'api/prod-items/create',
    			data: {product_packed : product_made }

    		}).then(function(response){
                if(response.data.status == 'warning' ){
                     Swal.fire({
                            type: 'warning',
                            title: 'DUPLICATES PRODUCT',
                            text: response.data.msg
                    });
                }else{
                    recalculate();
                    getProductMade();
                }
    			
    		});
    	}

        $scope.updateProducts = function(product){

            $http({
                method : 'PUT',
                url: 'api/prod-items/' + product.id,
                data: { qty : product.qty ,
                    cost_per_unit : product.cost_per_unit,
                    selling_price : product.selling_price,
                    profit_margin: product.profit_margin,
                    unit_packed: product.unit_packed
               }

            }).then(function(response){
                    getProductMade();
                    getPM();
            });
        }

        $scope.removeProduct = function(id) {
             $http({
                method : 'DELETE',
                url: 'api/prod-items/' + id
            }).then(function(response){
                    recalculate();
                    getProductMade();
            } , function(error){

            });
        }

        function recalculate() {
             $http({
                method : 'GET',
                url: 'api/prod-items/recalculate'
            }).then(function(response){
            });

        }
        
        $scope.sumVolProduced = function(list){
            var total=0;
            angular.forEach(list , function(product_made){
                total+= parseFloat(product_made.qty*product_made.unit_packed);
            });
            return total;
        }

        $scope.sumUnitPacked = function(list){
            var total=0;
            angular.forEach(list , function(product_made){
                total+= parseFloat(product_made.unit_packed);
            });
            return total;
        }   

        $scope.sumQty = function(list) {
            var total=0;
            angular.forEach(list , function(product_made){
                total+= parseFloat(product_made.qty);
            });
            return total;
        }
	}]);
})();