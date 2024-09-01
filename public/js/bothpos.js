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
        
        $scope.saleTempId = function(sale_temp_id){

            $scope.items = [ ];
            $scope.tempid = sale_temp_id;
            $http({
                method: 'GET',
                url: 'api/item'
            }).then(function (response) {
                $scope.items = response.data;
                console.log(response.data);
            }, function (error) {
                console.log(error);
                alert('This is embarassing. An error has occurred. Please check the log for details');
            });

            $scope.saletemp = { };
            $scope.customers = [ ];
            $scope.currencies = [ ];
            $scope.saletempitems = [ ];
            $scope.newsaletemp = { };
            $scope.defaultCustomer;
            $scope.getData = function(){
                console.log("Sale temp id = "+$scope.tempid);
                $http({
                    method: 'GET',
                    url: 'api/saletemp/'+$scope.tempid
                }).then(function (response) {
                    $scope.saletemp = response.data.saletemp;
                    $scope.saletempitems = response.data.items;
                    $scope.customers = response.data.customers;
                    $scope.currencies = response.data.currencies;
                    console.log(response.data);
                }, function (error) {
                    console.log(error);
                });
            };

            $scope.getData();
            
            $scope.usebarcode = false;
            $http({
                method: 'GET',
                url: 'api/usebarcode'
            }).then(function (response) {
                $scope.usebarcode = response.data.usebarcode;
                if ($scope.usebarcode) {
                    // Kick off the interval
                    $scope.intervalFunction();
                }
                console.log(response.data);
            }, function (error) {
                console.log(error);
                alert('This is embarassing. An error has occurred. Please check the log for details');
            });

            // Function to replicate setInterval using $timeout service.
            $scope.intervalFunction = function(){
                $timeout(function() {
                  $scope.getData();
                  $scope.intervalFunction();
                }, 15000)
            };

            $scope.addSaleTemp = function(item, newsaletemp, tempid) {
                if (item.in_stock == 0.00) {
                    Swal.fire({
                        type: 'warning',
                        title: 'EMPTY STOCK...',
                        text: 'The stock of '+item.name+' is currently ZERO. Please Purchase new Stock.'
                    });
                }else{
                    if (item.price_per_unit == null) {
                        Swal.fire({
                            type: 'warning',
                            title: 'NO SELLING PRICE...',
                            text: 'Selling price for  '+item.name+' is not set. Please update price for this product.'
                        });
                    }else{
                        $scope.testword = 'Tester'+item.id+", "+item.buying_per_unit+", "+item.price_per_unit;
                        $http({
                            method: 'POST',
                            url: 'api/saletemp', 
                            data: { sale_temp_id: tempid, product_id: item.id, buying_per_unit: item.buying_per_unit, price_per_unit: item.price_per_unit }
                        }).then(function (response) {
                            if(response.data.status == 'DUPL') {
                                Swal.fire({
                                    type: 'info',
                                    title: 'DUPLICATES...',
                                    text: response.data.msg
                                });
                            }else if(response.data.status == 'WP') {
                                Swal.fire({
                                    type: 'info',
                                    title: 'WRONG PRICES...',
                                    text: response.data.msg
                                });        
                            }else{
                                $scope.getData();
                            }
                            console.log(response);
                        }, function (error) {
                            console.log(error);
                        });
                    }
                }
            }

            $scope.updateSaleTemp = function(newsaletemp) {
                // alert(newsaletemp.sold_in);
                $http({
                    method: 'PUT',
                    url: 'api/saletemp/' + newsaletemp.id,
                    data: { quantity_sold: newsaletemp.quantity_sold, product_unit_id: newsaletemp.product_unit_id, buying_price: newsaletemp.buying_per_unit * newsaletemp.quantity_sold, price_per_unit: newsaletemp.price_per_unit, price: newsaletemp.price_per_unit * newsaletemp.quantity_sold, discount: newsaletemp.discount, total_discount: newsaletemp.total_discount, sold_in: newsaletemp.sold_in, with_vat:newsaletemp.with_vat }
                }).then(function (response) {
                    if (response.data.status == 'LOW') {
                        Swal.fire({
                            type: 'info',
                            title: 'LOW STOCK...',
                            text: data.msg
                        });
                    }else if(response.data.status == 'SHARED') {
                        (async () => {
                            /* inputOptions can be an object or Promise */
                            const inputOptions = new Promise((resolve) => {
                              setTimeout(() => {
                                resolve({
                                  'Old': 'Sell Old Stock First',
                                  'New': 'Sell New Stock First',
                                })
                              }, 1000)
                            })

                            const { value: usestock } = await Swal.fire({
                              title: 'Different Stocks',
                              text: data.msg,
                              input: 'radio',
                              inputOptions: inputOptions,
                              inputValidator: (value) => {
                                if (!value) {
                                  return 'You need to choose something!'
                                }
                              }
                            })

                            if (usestock) {
                                $http({
                                    method: 'PUT',
                                    url: 'api/saletemp/' + newsaletemp.id,
                                    data: { used_stock: usestock }
                                }).then(function (response){
                                    $scope.getData();
                                });
                            }

                        })()
                    }else if(response.data.status == 'WRONG'){
                        Swal.fire({
                            type: 'info',
                            title: 'WRONG QTY...',
                            text: response.data.msg
                        });
                    }

                    $scope.getData();
                    console.log(response.data);
                }, function (error) {
                    console.log(error);
                });
            }   

            $scope.updateSaleTempDiscount = function(list) {
                // alert('Tester');
                var total=0;
                angular.forEach(list , function(newsaletemp){
                    total+= parseFloat(newsaletemp.price_per_unit * newsaletemp.quantity_sold);
                });
                var dpercent = $scope.total_discount/total;

                // alert('You have added a Discount of '+ Math.round(dpercent*100)+ '% to each item sold.');
                angular.forEach(list, function(newsaletemp){ 
                    $http({
                        method: 'PUT',
                        url: 'api/saletemp/' + newsaletemp.id,
                        data: { quantity_sold: newsaletemp.quantity_sold, buying_price: newsaletemp.buying_per_unit * newsaletemp.quantity_sold, price: newsaletemp.price_per_unit * newsaletemp.quantity_sold, discount: newsaletemp.price_per_unit*dpercent, total_discount: newsaletemp.price*dpercent, sold_in: newsaletemp.sold_in }
                    }).then(function (response) {
                        $scope.getData();
                    });
                });
            }     

            $scope.removeSaleTemp = function(id) {
                $http({
                    method: 'DELETE',
                    url: 'api/saletemp/'+id
                }).then(function(response) {
                    $scope.getData();
                });
            }


            $scope.sum = function(list) {
                var total=0;
                angular.forEach(list , function(newsaletemp) {
                    total+= parseFloat(newsaletemp.price_per_unit * newsaletemp.quantity_sold);
                });
                return total;
            }

            $scope.sumDiscount = function(list){
                var t_discount=0;
                angular.forEach(list, function(newsaletemp) {
                    t_discount+= parseFloat(newsaletemp.discount * newsaletemp.quantity_sold);
                });
                return t_discount;
            }

            $scope.sumVAT = function(list){
                var t_vat=0;
                angular.forEach(list, function(newsaletemp) {
                    t_vat+= parseFloat(newsaletemp.vat_amount);
                });
                return t_vat;
            }

            //Service Items
            $scope.servitems = [ ];
            $http({
                method: 'GET',
                url: 'api/servitem'
            }).then(function (response) {
                $scope.servitems = response.data;
                console.log(response.data);
            }, function (error) {
                console.log(error);
                alert('This is embarassing. An error has occurred. Please check the log for details');
            });

            $scope.servsaletempitems = [ ];
            $scope.newservsaletemp = { };
            
            $scope.getServData = function(){
                console.log("Sale temp id = "+$scope.tempid);
                $http({
                    method: 'GET',
                    url: 'api/servsaletemp/'+$scope.tempid
                }).then(function (response) {
                    $scope.saletemp = response.data.saletemp;
                    $scope.servsaletempitems = response.data.items;
                    $scope.customers = response.data.customers;
                    $scope.currencies = response.data.currencies;
                    console.log(response);
                }, function (error) {
                    console.log(error);
                });
            };
            $scope.getServData();

            $scope.addSaleServTemp = function(item, newservsaletemp, tempid) {
                
                $http({
                    method: 'POST',
                    url: 'api/servsaletemp',
                    data: { sale_temp_id: tempid, service_id: item.id, price: item.price }
                }).then(function (response) {
                    if(response.data.status == 'DUPL') {
                        Swal.fire({
                            type: 'info',
                            title: 'DUPLICATES...',
                            text: response.data.msg
                        });
                    }else{
                        $scope.getServData();
                    }
                });
            }

            $scope.updateSaleServTemp = function(newservsaletemp) {

                $http({
                    method: 'PUT',
                    url: 'api/servsaletemp/' + newservsaletemp.id, 
                    data: { no_of_repeatition: newservsaletemp.no_of_repeatition, servprice: newservsaletemp.price, discount: newservsaletemp.total_discount, with_vat: newservsaletemp.with_vat }
                }).then(function (response) {
                    $scope.getServData();
                });
            }    

            $scope.updateSaleServTempDiscount = function(list) {
                var total=0;
                angular.forEach(list , function(newservsaletemp){
                    total+= parseFloat(newservsaletemp.price * newservsaletemp.no_of_repeatition);
                });
                var dpercent = $scope.total_discount/total;

                angular.forEach(list, function(newservsaletemp){
                    $http({
                        method: 'PUT',
                        url: 'api/servsaletemp/' + newservsaletemp.id,
                        data: { no_of_repeatition: newservsaletemp.no_of_repeatition, discount: newservsaletemp.price*dpercent }
                    }).then(function (response) {
                        $scope.getServData();
                    });
                });
            }

            $scope.removeSaleServTemp = function(id) {
                $http.delete('api/servsaletemp/' + id).
                success(function(data, status, headers, config) {
                    $http.get('api/servsaletemp').success(function(data) {
                            $scope.servsaletemp = data;
                    });
                });
            }
            $scope.sumServ = function(list) {
                var total=0;
                angular.forEach(list , function(newservsaletemp){
                    total+= parseFloat(newservsaletemp.price * newservsaletemp.no_of_repeatition);
                });
                return total;
            }

            $scope.sumServDiscount = function(list){
                var t_discount=0;
                angular.forEach(list, function(newservsaletemp){
                    t_discount+= parseFloat(newservsaletemp.discount * newservsaletemp.no_of_repeatition);
                });
                return t_discount;
            }

            $scope.sumServVAT = function(list){
                var t_vat=0;
                angular.forEach(list, function(newservsaletemp){
                    t_vat+= parseFloat(newservsaletemp.vat_amount);
                });
                return t_vat;
            }

            $scope.updateSaleTempInfo = function(saletemp) {
                $http({
                    method: 'PUT',
                    url: 'pos/' + saletemp.id,
                    data: { 
                        customer_id: saletemp.customer_id,
                        date_set: saletemp.date_set,
                        sale_date: saletemp.sale_date,
                        sale_type: saletemp.sale_type,
                        pay_type: saletemp.pay_type,
                        currency: saletemp.currency,
                        ex_rate_mode: saletemp.ex_rate_mode,
                        local_ex_rate: saletemp.local_ex_rate,
                        foreign_ex_rate: saletemp.foreign_ex_rate,
                        due_date: saletemp.due_date,
                        comments: saletemp.comments
                    }
                }).then(function (response) {
                    $scope.getData();
                    $scope.getServData();
                });       
            }
        };
    }]);
})();