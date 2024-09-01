@extends('layouts.vfd')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- Load Javascript Libraries (AngularJS, JQuery, Bootstrap) -->
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.7.8/angular.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.7.8/angular-animate.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.7.8/angular-route.min.js"></script>
    <script type="text/javascript" src="{{ asset('js/vfd-pos.js') }}"></script>

    <script>
        function validateform(form) {
            var items = document.saleform.no_items.value;
            if (items == 0) {
                // alert('Please select at least one item to continue.');
                Swal.fire(
                  'Nothing To Submit!',
                  'Please select at least one item to continue.',
                  'info'
                )
                return false;
            }

            form.myButton.disabled = true;
            form.myButton.value = "Please wait...";
            return true;
        }

        function confirmCancel() {
            Swal.fire({
              title: "{{trans('navmenu.are_you_sure')}}",
              text: "{{trans('navmenu.no_revert')}}",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: "{{trans('navmenu.cancel_it')}}",
              cancelButtonText: "{{trans('navmenu.no')}}"
            }).then((result) => {
              if (result.value) {
                window.location.href="{{url('cancel-sale')}}";
                Swal.fire(
                  "{{trans('navmenu.deleted')}}",
                  "{{trans('navmenu.cancelled')}}",
                  'success'
                )
              }
            })
        }


        function weg(elem) {
          var x = document.getElementById("sale_date_field");
          if(elem.value !== "auto") {
            x.style.display = "block";
          } else {
            x.style.display = "none";
            $("#sale_date").val('');
          }
        }


        function wegDam(elem) {
          var x = document.getElementById("dam_date_field");
          if(elem.value !== "auto") {
            x.style.display = "block";
          } else {
            x.style.display = "none";
            $("#dam_date").val('');
          }
        }

        function wegSaleType(elem) {
            var stype = "<?php echo $shop->subscription_type_id; ?>";
            var isfill = "<?php echo $settings->is_filling_station; ?>";
            var pm = document.getElementById('paymode');
            var z = document.getElementById('amount_paid');
            var c = document.getElementById('amount_due');
            var iv = document.getElementById('invoice_no');
            var vehc = document.getElementById('vehcleno');
            var d = document.getElementById('duedate');
            var b = document.getElementById('payable');
            var p = document.getElementById('paid');
            var bk = document.getElementById('bankdetail');
            var mo = document.getElementById('mobaccount');
            if (stype == 1) {
                if (elem.value === "credit") {
                    c.style.display = "block";
                    z.style.display = "block";
                    p.style.display = "none";
                }else if (elem.value === 'cash') {
                    z.style.display = "none";
                    c.style.display = "none";
                    p.style.display = "block";
                }else{
                    z.style.display = "none";
                    c.style.display = "none";
                    p.style.display = "none";
                }
            }else{
                if (elem.value === "credit") {
                    pm.style.display = "none";
                    // c.style.display = "block";
                    z.style.display = "none";
                    d.style.display = "block";
                    if (isfill) {
                        vehc.style.display = "block";
                    }
                    // p.style.display = "none";
                    bk.style.display = "none";
                    mo.style.display = "none";
                }else if (elem.value === 'cash') {
                    pm.style.display = "block";
                    z.style.display = "none";
                    // c.style.display = "none";
                    d.style.display = "none";
                    vehc.style.display = "none";
                    // p.style.display = "block";
                }else{
                    pm.style.display = "none";
                    z.style.display = "none";
                    // c.style.display = "none";
                    d.style.display = "none";
                    vehc.style.display = "none";
                    // p.style.display = "none";
                }
            }
        }

        function discountMode(elem) {
          var x = document.getElementById("total_discount_field");
          var y = document.getElementById('total_discount_value');
          var df = document.getElementById('discount_field');
          var dv = document.getElementById('discount_value');
          if(elem.value === "total") {
            x.style.display = "block";
            y.style.display = "none";
            dv.style.display = "block";
            df.style.display = "none";
          } else if (elem.value === "single") {
            x.style.display = "none";
            y.style.display = "block";
            df.style.display = "block";
            dv.style.display = "none";
          }
        }

        function detailUpdate(elem) {
            var b = document.getElementById('bankdetail');
            var m = document.getElementById('mobaccount');

            var dpm = document.getElementById('deposit_mode');
            var chq = document.getElementById('cheque');
            var slip = document.getElementById('slip');
            var expire = document.getElementById('expire');
            if (elem.value === 'Bank' || elem.value === 'Cheque') {
                b.style.display = 'block';
                m.style.display = 'none';
                if (elem.value === 'Bank') {
                    dpm.style.display = "block";
                    slip.style.display = 'block'
                    chq.style.display = 'none';
                    expire.style.display = "none";
                }else{
                    dpm.style.display = 'none';
                    slip.style.display = "none";
                    chq.style.display = "block";
                    expire.style.display = "block";
                }
            }else if (elem.value === 'Mobile Money') {
                b.style.display = 'none';
                m.style.display = 'block';
            }else{
                b.style.display = 'none';
                m.style.display = 'none';
            }
        }

    </script> 

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3"></div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{url('home')}}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row" ng-controller="SearchItemCtrl">
        <div class="col-xl-3 mx-auto">
            <h6 class="mb-0 text-uppercase">{{trans('navmenu.search_product')}}</h6>
            <hr>
            <div class="card">
                <div class="card-body">
                    <div class="col-sm-12">
                        <label class="form-label">{{trans('navmenu.search_product')}}</label> 
                        <input ng-model="searchKeyword" placeholder="{{trans('navmenu.search_product')}}" class="form-control form-control-sm mb-3">
                    </div>    
                    <table class="table table-hover" style="width: 100%; display: block; overflow: scroll; overflow: auto;">
                        <tr ng-repeat="item in items  | filter: searchKeyword | limitTo:10">
                            <td>
                                @{{item.desc}}
                            </td>
                            <td>
                                <a class="btn btn-success" ng-click="addVfdSaleTemp(item, newvfdsaletemp)"><span class="lni lni-arrow-right" aria-hidden="true"></span></a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-9 mx-auto">
            <h6 class="mb-0 text-uppercase">{{trans('navmenu.sale_items')}}</h6>
            <hr>
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form class="row g-3"  name="saleform" method="POST" action="{{ route('vfd-rct-infos.store') }}" onsubmit="return validateform(this)">
                            @csrf
                            <div class="col-sm-3">
                                <label for="invoice" class="form-label">{{trans('navmenu.receipt_no')}}.</label>
                                <input type="text" class="form-control form-control-sm mb-3" id="invoice" value="{{$rctnum}}" readonly/>
                            </div>
                            <div class="col-sm-3">
                                <label for="employee" class="form-label">{{trans('navmenu.user')}}</label>
                                <input type="text" class="form-control form-control-sm mb-3" id="employee" value="{{ Auth::user()->first_name }}" readonly/>
                            </div>

                            <div class="col-sm-6">
                                <label for="customer_id" class="form-label">{{trans('navmenu.customer')}}</label>
                                <input type="text" name="name" class="form-control form-control-sm mb-3" placeholder="Enter Customer Name">
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">{{trans('navmenu.cust_id_type')}}</label>
                                <select class="form-select" name="cust_id_type">
                                    @foreach($custids as $cid)
                                    @if($cid['id'] == 6)
                                    <option value="{{$cid['id']}}" selected>{{$cid['name']}}</option>
                                    @else
                                    <option value="{{$cid['id']}}">{{$cid['name']}}</option>
                                    @endif
                                    @endforeach                                    
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">{{trans('navmenu.id_number')}}</label>
                                <input type="text" name="custid" placeholder="{{trans('navmenu.hnt_id_number')}}" class="form-control form-control-sm mb-3">
                            </div>
                            <div class="col-sm-3">
                                <label for="inputEmailAddress" class="form-label">{{trans('navmenu.mobile')}} <span style="color:red">*</span></label>
                                <input type="tel" class="form-control form-control-sm mb-3" name="mobile" placeholder="Eg. 0789XXXXXX" value="{{old('phone')}}">
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">{{trans('navmenu.sales_type')}}</label>
                                <select name="sale_type" id="sale_type" onchange="wegSaleType(this)" class="form-select form-select-sm mb-3" required>
                                    <option value="">{{trans('navmenu.select_sale_type')}}</option>
                                    <option value="cash">{{trans('navmenu.cash_sales')}}</option>
                                    <option value="credit">{{trans('navmenu.credit_sales')}}</option>
                                </select>
                            </div>
                                    
                            <div class="col-sm-3" id="paymode" style="display: none;">
                                <label for="payment_type" class="form-label">{{trans('navmenu.pay_method')}}</label>
                                <select class="form-select form-select-sm mb-3" name="pay_type" required>
                                    <option>CASH</option>
                                    <option>CHEQUE</option>
                                    <option>CCARD</option>
                                    <option>EMONEY</option>
                                </select>
                            </div>
                            <div class="col-sm-12">
                                <table id="discount_field" class="table table-responsive table-striped display nowrap" style="width: 100%; display: block; overflow: scroll; overflow: auto;">
                                    <tr>
                                        <th>#</th>
                                        <th style="text-align: center;">ID</th>
                                        <th style="text-align: center;">DESC</th>
                                        <th style="text-align: center;">QTY</th>
                                        <th style="text-align: center;">PRICE</th>
                                        <th style="text-align: center;">TOTAL</th>
                                        @if($settings->is_vat_registered)
                                        <th style="text-align: center;">{{trans('navmenu.add_vat')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.vat')}}</th>
                                        @endif
                                        <th>&nbsp;</th>
                                    </tr>
                                    <tr ng-repeat="newvfdsaletemp in vfdsaletemp" id="temps">
                                        <td>@{{$index + 1}}</td>
                                        <td>@{{newvfdsaletemp.item_code}}</td>
                                        <td>@{{newvfdsaletemp.desc}}</td>
                                        <td><input type="number" class="form-control form-control-sm mb-1" style="text-align:center; width: 140px;" autocomplete="off" name="qty" ng-blur="updateVfdSaleTemp(newvfdsaletemp)" string-to-number ng-model="newvfdsaletemp.qty" min="0" step="any" value="@{{newvfdsaletemp.qty}}"></td>
                                        <td>
                                            <input type="number" class="form-control form-control-sm mb-1" style="text-align:center; width: 140px;" name="price" ng-blur="updateVfdSaleTemp(newvfdsaletemp)" string-to-number ng-model="newvfdsaletemp.price" value="@{{newvfdsaletemp.price}}">
                                        </td>
                                        <td>@{{(newvfdsaletemp.price * newvfdsaletemp.qty) | number:2}}</td>
                                        @if($settings->is_vat_registered)
                                        <td ng-if="newvfdsaletemp.with_vat == 'no'"><select ng-model="newvfdsaletemp.with_vat" name="with_vat" ng-change="updateVfdSaleTemp(newvfdsaletemp)" style="border: 1px solid #e0e0e0;">
                                            <option value="no">{{trans('navmenu.no')}}</option>
                                            <option value="yes">{{trans('navmenu.yes')}}</option>
                                        </select></td>
                                        <td ng-if="newvfdsaletemp.with_vat == 'yes'"><select ng-model="newvfdsaletemp.with_vat" name="with_vat" ng-change="updateVfdSaleTemp(newvfdsaletemp)" style="border: 1px solid #e0e0e0;">
                                            <option value="yes">{{trans('navmenu.yes')}}</option>
                                            <option value="no">{{trans('navmenu.no')}}</option>
                                        </select></td>
                                        <td>@{{newvfdsaletemp.vat | number:2}}</td>
                                        @endif
                                
                                        <td><a href="#" ng-click="removeVfdSaleTemp(newvfdsaletemp.id)"><span class="bx bx-trash" aria-hidden="true" style="color: red"></span></a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="col-sm-6">
                                        <label for="items" class="form-label">{{trans('navmenu.selected_items')}}</label>
                                        <input type="text" id="no_items" name="no_items" value="@{{vfdsaletemp.length}}" class="form-control form-control-sm mb-3" readonly>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <table class="table " style="width: 100%;">
                                        <tr>
                                            <th>{{trans('navmenu.subtotal')}}</th>
                                            <th style="text-align: right;"><b>@{{sum(vfdsaletemp) | number:2}}</b></th>
                                        </tr>
                                        <tr>
                                            <th>{{trans('navmenu.discount')}}</th>
                                            <th><input type="number" style="text-align: right;" name="discount" ng-blur="updateDiscount(discount)" string-to-number ng-model="discount" value="@{{sumDiscount(vfdsaletemp)}}" class="form-control form-control-sm mb-3" autocomplete="off"></th>
                                        </tr>
                                        <tr>
                                            <th>{{trans('navmenu.vat')}}</th>
                                            <th style="text-align: right;"><b>@{{sumVAT(vfdsaletemp) | number:2}}</b></th>
                                        </tr>
                                        <tr>
                                            <th>{{trans('navmenu.total')}}</th>
                                            <th style="text-align: right;"><b>@{{(sum(vfdsaletemp)-discount+sumVAT(vfdsaletemp)) | number:2}}</b></th>
                                        </tr>     
                                    </table>

                                    <div class="row">
                                        <div class="col-sm-4" style="margin-top: 5px;">
                                            <button type="submit" name="myButton" class="btn btn-success btn-block">{{trans('navmenu.btn_submit')}}</button>
                                        </div>
                                        
                                        <div class="col-sm-4" style="margin-top: 5px;">
                                            <button onclick="confirmCancel()" type="button" class="btn btn-warning btn-block">{{trans('navmenu.btn_cancel')}}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection