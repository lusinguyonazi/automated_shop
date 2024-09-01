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

    app.controller("SearchItemCtrl", [ '$scope', '$http', '$filter', '$timeout', function($scope, $http, $filter, $timeout) {
        $scope.purchaseTempId = function(purchase_temp_id) {

            $scope.items = [ ];
            $scope.tempid = purchase_temp_id;

            $http({
                method: 'GET',
                url: 'api/item'
            }).then(function (response) {
                $scope.items = response.data;
                console.log(response);
            }, function (error) {

            });

            $scope.purchasetemp = { };
            $scope.suppliers = [ ];
            $scope.currencies = [ ];
            $scope.stocktempitems = [ ];
            $scope.newstocktemp = { };

            $scope.getData = function() {
                console.log("Purchase Temp id = "+$scope.tempid);
                $http({
                    method: 'GET',
                    url: 'api/stocktemp/'+$scope.tempid
                }).then(function (response) {
                    $scope.purchasetemp = response.data.purchasetemp;
                    $scope.stocktempitems = response.data.items;
                    $scope.currencies = response.data.currencies;
                    $scope.suppliers = response.data.suppliers;                    
                });
            };

            $scope.getData();

            $scope.usebarcode = false;

            $http({
                method : 'GET',
                url : 'api/usebarcode'
            }).then(function(response) {
                $scope.usebarcode = response.data.usebarcode;
                if($scope.usebarcode){
                    $scope.intervalFunction();
                }
            } , function(error) {

            });

            // Function to replicate setInterval using $timeout service.
            $scope.intervalFunction = function(){
                $timeout(function() {
                  $scope.getData();
                  $scope.intervalFunction();
                }, 15000)
            };
            
            $scope.addStockTemp = function(item, newstocktemp, tempid) {
                $http({
                    method : 'POST',
                    url : 'api/stocktemp',
                    data : { purchase_temp_id: tempid, product_id: item.id, quantity_in: 0 } 
                }).then(function(response){
                     if(response.data.status == 'DUPL') {
                        Swal.fire({
                            type: 'warning',
                            title: 'DULICATION',
                            text: response.data.msg
                        });
                     }else {
                        $scope.getData();
                    }
                }, function(error){
                    console.log(error);
                });
            }

            $scope.updateStockTemp = function(newstocktemp) {
                if (newstocktemp.expire_date != null) {
                    var is_valid = isValidDate(newstocktemp.expire_date);
                    if (!is_valid) {
                        Swal.fire({
                            type: 'info',
                            title: 'WRONG DATE...'+newstocktemp.expire_date,
                            text: 'You have entered invalid expire date. Please enter a valid expire date for ' +newstocktemp.product.name+ "'s Stock entry using the format in the text field."
                        });
                    }
                }
                
                $http({
                    method : 'PUT',
                    url : 'api/stocktemp/'+ newstocktemp.id ,
                    data: {quantity_in: newstocktemp.quantity_in, buying_per_unit: newstocktemp.buying_per_unit, total: newstocktemp.total, price_per_unit: newstocktemp.price_per_unit, expire_date: newstocktemp.expire_date} 
                }).then(function (response){
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
                            text: 'You have entered invalid expire date. Please enter a valid expire date for ' +newstocktemp.product.name+ "'s Stock entry using the format in the text field."
                        });
                    }

                    $scope.getData();
                });
            }   

            $scope.removeStockTemp = function(id) {
                $http({
                    method: 'DELETE',
                    url: 'api/stocktemp/' + id ,
                }).then(function (response){
                   $scope.getData(); 
                });
            }

            $scope.sum = function(list) {
                var total=0;
                angular.forEach(list , function(newstocktemp){
                    total+= parseFloat(newstocktemp.total);
                });
                return total;
            }

            $scope.updatePurchaseTempInfo = function(purchasetemp) {
                $http({
                    method: 'POST',
                    url: 'api/update-purchase-temp',
                    data: { 
                        id: purchasetemp.id,
                        supplier_id: purchasetemp.supplier_id,
                        date_set: purchasetemp.date_set,
                        purchase_date: purchasetemp.purchase_date,
                        purchase_type: purchasetemp.purchase_type,
                        pay_type: purchasetemp.pay_type,
                        currency: purchasetemp.currency,
                        ex_rate_mode: purchasetemp.ex_rate_mode,
                        local_ex_rate: purchasetemp.local_ex_rate,
                        foreign_ex_rate: purchasetemp.foreign_ex_rate,
                        comments: purchasetemp.comments
                    }
                }).then(function (response) {
                    $scope.getData();
                });       
            }
        };
    }]);
    
})();

// Validates that the input string is a valid date formatted as "mm/dd/yyyy"
function isValidDate(dateString)
{
    // First check for the pattern
    if(!/^\d{4}\-\d{1,2}\-\d{1,2}$/.test(dateString))
        return false;

    // Parse the date parts to integers
    var parts = dateString.split("-");
    var day = parseInt(parts[2], 10);
    var month = parseInt(parts[1], 10);
    var year = parseInt(parts[0], 10);

    // Check the ranges of month and year
    if(year < 2020 || year > 3000 || month == 0 || month > 12)
        return false;

    var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];

    // Adjust for leap years
    if(year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
        monthLength[1] = 29;

    // Check the range of the day
    return day > 0 && day <= monthLength[month - 1];
};