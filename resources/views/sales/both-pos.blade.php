@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('js/bothpos.js') }}"></script>
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
                // window.location.href="{{url('cancel-sale')}}";
                document.getElementById('delete-form').submit();
                Swal.fire(
                  "{{trans('navmenu.deleted')}}",
                  "{{trans('navmenu.cancelled')}}",
                  'success'
                )
              }
            })
        }

        function submitTemp(index) {
            document.getElementById('ptemp-form-'+index).submit();
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
            var b = document.getElementById('bank-name');
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
                dpm.style.display = 'none';
                chq.style.display = 'none';
                slip.style.display = 'none';
                expire.style.display = 'none';
            }else{
                b.style.display = 'none';
                m.style.display = 'none';
                dpm.style.display = 'none';
                chq.style.display = 'none';
                slip.style.display = 'none';
                expire.style.display = 'none';
            }
        }

        function rateMode(val){
            var localRate = document.getElementById('local-rate');
            var foreignRate = document.getElementById('foreign-rate');

            if (val == 'default') {
                foreignRate.style.display = 'none';
                localRate.style.display = 'block';
            }else{
                localRate.style.display = 'none';
                foreignRate.style.display = 'block';
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
        <div class="ms-auto">
            
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row" ng-controller="SearchItemCtrl" ng-init="saleTempId('<?php echo $saletemp->id; ?>')">
        <div class="col-xl-3 mx-auto">
            <h6 class="mb-0 text-uppercase">{{trans('navmenu.search_product')}}</h6>
            <hr>
            <div class="card">
                <div class="card-body">
                    <div class="col-sm-12">
                        <label class="form-label">Scan Barcode</label>
                        <input id="scanner_input" name="barcode" type="text" ng-model="barcode" class="form-control form-control-sm mb-3" placeholder="Scan barcode from an item ..." type="text" autofocus/>
                    </div>
                    <div class="col-sm-12">
                        <label class="form-label">{{trans('navmenu.search_tap')}}</label> 
                        <input ng-model="searchKeyword" placeholder="{{trans('navmenu.search_product')}}" class="form-control form-control-sm mb-3">
                    </div>
                    <div class="col-sm-12">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center" ng-repeat="item in items  | filter: searchKeyword | limitTo:10" ng-click="addSaleTemp(item, newsaletemp, tempid)">
                                <div class="col-sm-11">
                                    @{{item.name}}
                                    @if(Auth::user()->roles[0]['name'] == 'manager' || Auth::user()->can('view-stock'))
                                    <span style="color: blue;" ng-if="item.in_stock > 0">(@{{item.in_stock}})</span>
                                    <span style="color: red;" ng-if="item.in_stock == 0">(@{{item.in_stock}})</span>
                                    @endif <br><small style="color: #757575;">@{{item.product_no}}</small>
                                </div>
                                <div class="col-sm-1">
                                    <span class="badge bg-success rounded-pill"><span class="bx bx-redo" aria-hidden="true"></span></span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <h6 class="mb-0 text-uppercase">{{trans('navmenu.search_service')}}</h6>
            <hr>
            <div class="card">
                <div class="card-body">
                    <div class="col-sm-12">
                        <label class="form-label">{{trans('navmenu.search_tap')}}</label> 
                        <input ng-model="searchKeyword" placeholder="{{trans('navmenu.search_service')}}" class="form-control form-control-sm mb-3">
                    </div>  
                    <div class="col-sm-12">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center" ng-repeat="servitem in servitems  | filter: searchKeyword | limitTo:10" ng-click="addSaleServTemp(servitem, newservsaletemp, tempid)">
                                <div class="col-sm-10">
                                    @{{servitem.name}}<br>
                                    <small style="color: #757575;">@{{item.description}}</small>
                                </div>
                                <div class="col-sm-1">
                                    <span class="badge bg-warning rounded-pill"><span class="bx bx-redo" aria-hidden="true"></span></span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-9 mx-auto">
            <h6 class="mb-0 text-uppercase">{{trans('navmenu.sale_items')}}</h6>
            <hr>
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-6 d-lg-flex align-items-center mb-1 gap-1">
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#customerModal"><i class="bx bx-user-plus"></i>{{trans('navmenu.new_customer')}}</button>
                            @if($settings->is_agent)
                            <a href="{{url('ocamounts')}}" class="btn btn-primary pull-right" style="margin-left: 5px;"><i class="fa fa-file-o"></i>{{trans('navmenu.new_oc_amount')}}</a>
                            @endif
                        </div>
                        <div class="btn-group col-sm-6" role="group">
                            <button type="button" class="btn btn-outline-danger btn-sm">{{$pendingtemps->count()}}</button>
                            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="dropdown">Pending Bills/Invoices <i class="bx bx-caret-down"></i></button>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end"> 
                                @foreach($pendingtemps as $key => $temp) 
                                <form class="row g-3" method="POST" action="{{'pt-pos'}}" id="ptemp-form-{{$key}}">
                                    @csrf
                                    <input type="hidden" name="id" value="{{$temp->id}}">
                                    <a class="dropdown-item" href="javascript:;" onclick="submitTemp('<?php echo $key; ?>')">{{$temp->name}} (<span class="badge rounded-pill bg-warning text-dark"> Created since {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $temp->created_at)->diffForHumans() }}</span>)</a>
                                </form>  
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="p-3 border rounded">
                        <form class="row g-3"  name="saleform" method="POST" action="{{ route('pos.store') }}" onsubmit="return validateform(this)" ng-if="saletemp">
                            @csrf
                            <input type="hidden" name="sale_temp_id" placeholder="" value="{{$saletemp->id}}" class="form-control form-control-sm mb-3">
                            <div class="col-sm-6">
                                <label for="customer_id" class="form-label">{{trans('navmenu.customer')}} <span style="color: red;">*</span></label>
                                <select name="customer_id" id="customer_id" required class="form-select form-select-sm mb-3" ng-model="saletemp.customer_id" ng-change="updateSaleTempInfo(saletemp)" ng-options="customer.id as customer.name for customer in customers">
                                    <option value="">---{{trans('navmenu.select')}}---</option>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <label for="invoice" class="form-label">{{trans('navmenu.saledate')}}</label>
                                <select name="date_set" id="date_set" ng-model="saletemp.date_set" ng-change="updateSaleTempInfo(saletemp)" onchange="weg(this)" class="form-select form-select-sm mb-3">
                                    <option value="auto">Auto</option>
                                    <option value="manual">Manual</option>
                                </select>
                            </div>
                            <div class="col-sm-3" id="sale_date_field" style="display: none;">
                                <label class="form-label">{{trans('navmenu.pick_date')}}</label>
                                <div class="inner-addon left-addon"> 
                                    <i class="myaddon bx bx-calendar"></i>
                                    <input type="text" name="sale_date" id="sale_date" ng-model="saletemp.sale_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-3">
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <label class="form-label">{{trans('navmenu.sales_type')}}</label>
                                <select name="sale_type" id="sale_type" onchange="wegSaleType(this)" class="form-select form-select-sm mb-3" ng-model="saletemp.sale_type" ng-change="updateSaleTempInfo(saletemp)" required>
                                    <option value="">---{{trans('navmenu.select')}}---</option>
                                    <option value="cash">{{trans('navmenu.cash_sales')}}</option>
                                    <option value="credit">{{trans('navmenu.credit_sales')}}</option>
                                </select>
                            </div>
                            <div class="col-sm-3" id="duedate" style="display: none;">
                                <label for="total" class="form-label">{{trans('navmenu.due_date')}} <span style="color: red;">*</span></label>
                                <div class="inner-addon left-addon"> 
                                    <i class="myaddon bx bx-calendar"></i>
                                    <input type="text" name="due_date" ng-model="saletemp.due_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-3">
                                </div>
                            </div>
                                        
                                   
                            <div class="col-sm-3" id="paymode" style="display: none;">
                                <label for="payment_type" class="form-label">{{trans('navmenu.pay_method')}}</label>
                                <select class="form-select form-select-sm mb-1" name="pay_type" ng-model="saletemp.pay_type" ng-change="updateSaleTempInfo(saletemp)" onchange="detailUpdate(this)" required>
                                    <option value="Cash" selected>{{trans('navmenu.cash')}}</option>
                                    <option value="Cheque">{{trans('navmenu.cheque')}}</option>
                                    <option value="Bank">{{trans('navmenu.bank')}}</option>
                                    <option value="Mobile Money">{{trans('navmenu.mobilemoney')}}</option>
                                </select>
                            </div>
                            <div class="col-sm-3" id="deposit_mode" style="display: none;">
                                <label class="form-label">Deposit Mode</label>
                                <select name="deposit_mode" class="form-select form-select-sm mb-1">
                                    <option>Direct Deposit</option>
                                    <option>Bank Transfer</option>
                                </select>
                            </div>
                            <div class="col-sm-3" id="bank-name" style="display: none;">
                                <label class="form-label">Bank Name </label>
                                <select name="bank_name" class="form-select form-select-sm mb-1">
                                    <option value="">---{{trans('navmenu.select')}}---</option>
                                    @foreach($bdetails as $detail)
                                    <option value="{{$detail->id}}">{{$detail->bank_name}} - {{$detail->branch_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-3" id="cheque" style="display: none;">
                                <label class="form-label">Cheque Number</label>
                                <input id="name" type="text" name="cheque_no" placeholder="Please enter Cheque Number" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-3" id="expire" style="display: none;">
                                <label class="form-label">Expire Date</label>
                                <div class="inner-addon left-addon"> 
                                    <i class="myaddon bx bx-calendar"></i>
                                    <input id="name" type="text" name="expire_date" placeholder="Please enter Expire Date" class="form-control form-control-sm mb-1">
                                </div>
                            </div>
                            <div class="col-sm-3" id="slip" style="display: none;">
                                <label class="form-label">Reference Number</label>
                                <input id="name" type="text" name="slip_no" placeholder="Enter Slip number" class="form-control form-control-sm mb-1">
                            </div>
                            <div class="col-sm-3" id="mobaccount" style="display: none;">
                                <label class="form-label">Mobile Money Operator </label>
                                <select class="form-select form-select-sm mb-1" name="operator">
                                    <option value="">---{{trans('navmenu.select')}}---</option>
                                    <option>AirtelMoney</option>
                                    <option>EzyPesa</option>
                                    <option>M-Pesa</option>
                                    <option>TigoPesa</option>
                                    <option>HaloPesa</option>
                                </select>
                            </div>
                            @if($settings->allow_multi_currency)
                            <div class="col-sm-3">
                                <label class="form-label">{{trans('navmenu.currency')}}</label>
                                <select name="currency" id="currency" class="form-select form-select-sm mb-3" ng-model="saletemp.currency" ng-change="updateSaleTempInfo(saletemp)" ng-options="curr.code as curr.code for curr in currencies" required>
                                </select>
                            </div>
                            <div class="col-sm-3" ng-if="saletemp.currency != saletemp.defcurr">
                                <label class="form-label">Exchange Rate Mode</label>
                                <select name="ex_rate_mode"  class="form-select form-select-sm mb-3" ng-model="saletemp.ex_rate_mode">
                                    <option value="Locale" selected>1 @{{saletemp.defcurr}} Equals ? @{{saletemp.currency}}</option>
                                    <option value="Foreign">1 @{{saletemp.currency}} Equals ? @{{saletemp.defcurr}}</option>
                                </select>
                            </div>
                            <div class="col-sm-3" ng-if="saletemp.currency != saletemp.defcurr && saletemp.ex_rate_mode == 'Locale'">
                                <label class="form-label">Rate Amount in @{{saletemp.currency}}</label>
                                <input id="foreign-ex-rate" type="number" min="0" step="any" name="foreign_ex_rate" class="form-control form-control-sm mb-3" string-to-number ng-model="saletemp.foreign_ex_rate" ng-blur="updateSaleTempInfo(saletemp)">
                            </div>
                            <div class="col-sm-3" ng-if="saletemp.currency != saletemp.defcurr && saletemp.ex_rate_mode == 'Foreign'">
                                <label class="form-label">Rate Amount in @{{saletemp.defcurr}}</label>
                                <input id="local-ex-rate" type="number" min="0" step="any" name="local_ex_rate" class="form-control form-control-sm mb-3" string-to-number ng-model="saletemp.local_ex_rate" ng-blur="updateSaleTempInfo(saletemp)">
                            </div>
                            @endif
                            <div class="col-sm-3">
                                <label for="invoice" class="form-label">{{trans('navmenu.discount_by')}}:</label>
                                <select name="disc_set" id="disc_set" onchange="discountMode(this)" class="form-select form-select-sm mb-3">
                                    <option value="single">{{trans('navmenu.each_product')}}</option>
                                    <option value="total">{{trans('navmenu.total_sale')}}</option>
                                </select>
                            </div>
                            
                            <div class="col-sm-12" id="discount_field">
                                <table class="table table-responsive table-striped display nowrap" style="width: 100%; display: block; overflow: scroll; overflow: auto;">
                                    <tr>
                                        <th>#</th>
                                        <th style="text-align: center;">{{trans('navmenu.item_name')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.unit')}}</th>
                                        @if($settings->retail_with_wholesale)
                                        <th style="text-align: center;">{{trans('navmenu.sold_in')}}</th>
                                        @endif
                                        <th style="text-align: center;">{{trans('navmenu.price')}}</th>
                                        @if($settings->allow_unit_discount)
                                        <th style="text-align: center;">{{trans('navmenu.unit_discount')}}</th>
                                        @endif
                                        <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.discount')}}</th>
                                        @if($settings->is_vat_registered)
                                        <th style="text-align: center;">{{trans('navmenu.add_vat')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.vat')}}</th>
                                        @endif
                                        <th>&nbsp;</th>
                                    </tr>
                                    <tr ng-repeat="newsaletemp in saletempitems" id="temps">
                                        <td>@{{$index + 1}}</td>
                                        <td>@{{newsaletemp.name}}</td>
                                        <td><input type="number" class="form-control form-control-sm mb-1" style="text-align:center; width: 140px;" autocomplete="off" name="quantity_sold" ng-blur="updateSaleTemp(newsaletemp)" string-to-number ng-model="newsaletemp.quantity_sold" min="0" step="any" value="@{{newsaletemp.quantity_sold}}"></td>
                                        <td>
                                            <select ng-model="newsaletemp.product_unit_id" name="product_unit_id" ng-change="updateSaleTemp(newsaletemp)" class="form-select form-select-sm mb-1" ng-options="unit.id as unit.unit_name for unit in newsaletemp.units" style="width: 70px;">
                                                
                                            </select>
                                        </td>
                                        @if($settings->retail_with_wholesale)
                                        <td><select ng-model="newsaletemp.sold_in" name="sold_in" ng-change="updateSaleTemp(newsaletemp)" class="form-select form-select-sm mb-1" style="border: 1px solid #e0e0e0; width: 120px;">
                                            <option value="Retail Price">{{trans('navmenu.retail_price')}}</option>
                                            <option value="Wholesale Price">{{trans('navmenu.wholesaleprice')}}</option>
                                        </select></td>
                                        @endif
                                        <td>
                                        @if($settings->enable_cpos)
                                            <input type="number" min="0" step="any" class="form-control form-control-sm mb-1" style="text-align:center; width: 140px;" name="price_per_unit" ng-blur="updateSaleTemp(newsaletemp)" ng-model="newsaletemp.price_per_unit">
                                        @else
                                            @{{newsaletemp.price_per_unit | number:2}}
                                        @endif
                                        </td>
                                        @if($settings->allow_unit_discount)
                                        <td><input type="number" min="0" step="any" class="form-control form-control-sm mb-1" style="text-align:center; width: 140px;" name="discount" ng-blur="updateSaleTemp(newsaletemp)" string-to-number ng-model="newsaletemp.discount"></td>
                                        @endif
                                        <td>@{{(newsaletemp.price_per_unit * newsaletemp.quantity_sold) | number:2}}</td>
                                        <td><input type="number" min="0" step="any" class="form-control form-control-sm mb-1" style="text-align:center; width: 140px;" name="total_discount" ng-blur="updateSaleTemp(newsaletemp)" string-to-number ng-model="newsaletemp.total_discount"></td>
                                        @if($settings->is_vat_registered)
                                        <td><select ng-model="newsaletemp.with_vat" name="with_vat" ng-change="updateSaleTemp(newsaletemp)" style="border: 1px solid #e0e0e0;">
                                            <option value="no">{{trans('navmenu.no')}}</option>
                                            <option value="yes">{{trans('navmenu.yes')}}</option>
                                        </select></td>
                                        <td>@{{newsaletemp.vat_amount | number:2}}</td>
                                        @endif
                                
                                        <td><a href="#" ng-click="removeSaleTemp(newsaletemp.id)"><span class="bx bx-trash" aria-hidden="true" style="color: red"></span></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th>{{trans('navmenu.total')}}</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th style="text-align: center;">@{{sum(saletempitems) | number:2}}</th>
                                        <th style="text-align: center;">@{{sumDiscount(saletempitems) | number:2}}</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </table>
                                <table class="table table-responsive table-striped display nowrap" style="width: 100%; display: block; overflow: scroll; overflow: auto;">
                                    <tr>
                                        <th>#</th>
                                        <th style="text-align: center;">{{trans('navmenu.service')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.price')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.discount')}}</th>
                                        @if($settings->is_vat_registered)
                                        <th style="text-align: center;">{{trans('navmenu.add_vat')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.vat')}}</th>
                                        @endif
                                        <th>&nbsp;</th>
                                    </tr>
                                    <tr ng-repeat="newservsaletemp in servsaletempitems" id="temps">
                                        <td>@{{$index + 1}}</td>
                                        <td>@{{newservsaletemp.name}}</td>
                                        <td><input type="number" style="text-align:center; height: 20px; width: 80px; border: 1px solid #e0e0e0;" autocomplete="off" name="no_of_repeatition" ng-blur="updateSaleServTemp(newservsaletemp)" string-to-number ng-model="newservsaletemp.no_of_repeatition" min="1" step="1" value="@{{newservsaletemp.no_of_repeatition}}" class="form-control form-control-sm mb-1"></td>
                                        <td>
                                        @if($settings->enable_cpos)
                                            <input type="number" min="0" step="any" style="text-align:center; height: 20px; width: 100px; border: 1px solid #e0e0e0;" name="price" ng-blur="updateSaleServTemp(newservsaletemp)" string-to-number ng-model="newservsaletemp.price" class="form-control form-control-sm mb-1">
                                        @else
                                            @{{newservsaletemp.price | number:2}}
                                        @endif
                                        </td>
                                        <td>@{{(newservsaletemp.price * newservsaletemp.no_of_repeatition) | number:2}}</td>
                                        <td><input type="number" min="0" step="any" style="text-align:center; height: 20px; width: 100px; border: 1px solid #e0e0e0;" name="total_discount" ng-blur="updateSaleServTemp(newservsaletemp)" string-to-number ng-model="newservsaletemp.total_discount" class="form-control form-control-sm mb-1"></td>
                                        @if($settings->is_vat_registered)
                                        <td><select ng-model="newservsaletemp.with_vat" name="sold_in" ng-change="updateSaleServTemp(newservsaletemp)" style="border: 1px solid #e0e0e0;">
                                            <option value="no">{{trans('navmenu.no')}}</option>
                                            <option value="yes">{{trans('navmenu.yes')}}</option>
                                        </select></td>
                                        <td>@{{newservsaletemp.vat_amount | number:2}}</td>
                                        @endif
                                        
                                        <td><a href="#" ng-click="removeSaleServTemp(newservsaletemp.id)"><span class="glyphicon glyphicon-trash" aria-hidden="true" style="color: red"></span></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th>{{trans('navmenu.total')}}</th>
                                        <th></th>
                                        <th></th>
                                        <th style="text-align: center;">@{{sumServ(servsaletempitems) | number:2}}</th>
                                        <th style="text-align: center;">@{{sumServDiscount(servsaletempitems) | number:2}}</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-12" id="discount_value" style="display: none;">
                                <table class="table table-responsive table-striped display nowrap" style="width: 100%; display: block; overflow: scroll; overflow: auto; ">
                                    <tr>
                                        <th>#</th>
                                        <th style="text-align: center;">{{trans('navmenu.item_name')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.unit')}}</th>
                                        @if($settings->retail_with_wholesale)
                                        <th style="text-align: center;">{{trans('navmenu.sold_in')}}</th>
                                        @endif
                                        <th style="text-align: center;">{{trans('navmenu.price')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.discount')}}</th>
                                        @if($settings->is_vat_registered)
                                        <th style="text-align: center;">{{trans('navmenu.add_vat')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.vat')}}</th>
                                        @endif
                                        <th>&nbsp;</th>
                                    </tr>
                                    <tr ng-repeat="newsaletemp in saletempitems" id="temps">
                                        <td>@{{$index + 1}}</td>
                                        <td>@{{newsaletemp.product.name}}</td>
                                        <td><input type="number" class="form-control form-control-sm mb-1" style="text-align:center; width: 140px;" autocomplete="off" name="quantity_sold" ng-blur="updateSaleTemp(newsaletemp)" string-to-number ng-model="newsaletemp.quantity_sold" min="0" step="any"></td>
                                        <td>
                                            <select ng-model="newsaletemp.product_unit_id" name="product_unit_id" ng-change="updateSaleTemp(newsaletemp)" class="form-select form-select-sm mb-1" ng-options="unit.id as unit.unit_name for unit in newsaletemp.units" style="width: 70px;">
                                                
                                            </select>
                                        </td>
                                        @if($settings->retail_with_wholesale)
                                        <td><select ng-model="newsaletemp.sold_in" name="sold_in" ng-change="updateSaleTemp(newsaletemp)" style="border: 1px solid #e0e0e0;">
                                            <option value="Retail Price">{{trans('navmenu.retail_price')}}</option>
                                            <option value="Wholesale Price">{{trans('navmenu.wholesaleprice')}}</option>
                                        </select></td>
                                        @endif
                                        <td>
                                        @if($settings->enable_cpos)
                                            <input type="number" min="0" step="any" style="text-align:center; height: 20px; width: 240px; border: 1px solid #e0e0e0;" name="price_per_unit" ng-blur="updateSaleTemp(newsaletemp)" string-to-number ng-model="newsaletemp.price_per_unit" class="form-control form-control-sm mb-1">
                                        @else
                                            @{{newsaletemp.price_per_unit | number:2}}
                                        @endif
                                        </td>
                                        <td>@{{(newsaletemp.price_per_unit * newsaletemp.quantity_sold) | number:2}}</td>
                                        <td>@{{newsaletemp.total_discount | number:2}}</td>
                                        @if($settings->is_vat_registered)
                                        <td><select ng-model="newsaletemp.with_vat" name="sold_in" ng-change="updateSaleTemp(newsaletemp)" style="border: 1px solid #e0e0e0;">
                                            <option value="no">{{trans('navmenu.no')}}</option>
                                            <option value="yes">{{trans('navmenu.yes')}}</option>
                                        </select></td>
                                        <td>@{{newsaletemp.vat_amount | number:2}}</td>
                                        @endif
                                        <td><a href="#" ng-click="removeSaleTemp(newsaletemp.id)"><span class="bx bx-trash" aria-hidden="true" style="color: red"></span></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th>{{trans('navmenu.total')}}</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th>@{{sum(saletempitems) | number:2}}</th>
                                        <th>
                                            <input type="number" step="any" style="text-align:left;" name="sale_discount" ng-blur="updateSaleTempDiscount(saletempitems)" ng-model="total_discount" value="@{{sumDiscount(saletempitems)}}" class="form-control form-control-sm mb-1">
                                        </th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </table>
                                <table class="table table-responsive table-striped display nowrap" style="width: 100%; display: block; overflow: scroll; overflow: auto;">
                                    <tr>
                                        <th>#</th>
                                        <th style="text-align: center;">{{trans('navmenu.service')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.price')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.discount')}}</th>
                                        @if($settings->is_vat_registered)
                                        <th style="text-align: center;">{{trans('navmenu.add_vat')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.vat')}}</th>
                                        @endif
                                        <th>&nbsp;</th>
                                    </tr>
                                    <tr ng-repeat="newservsaletemp in servsaletempitems" id="temps">
                                        <td>@{{$index + 1}}</td>
                                        <td>@{{newservsaletemp.service.name}}</td>
                                        <td><input type="number" style="text-align:center; height: 20px; width: 80px; border: 1px solid #e0e0e0;" autocomplete="off" name="no_of_repeatition" ng-blur="updateSaleServTemp(newservsaletemp)" string-to-number ng-model="newservsaletemp.no_of_repeatition" min="1" step="1" value="@{{newservsaletemp.no_of_repeatition}}" class="form-control form-control-sm mb-1"></td>
                                        <td>
                                        @if($settings->enable_cpos)
                                            <input type="number" min="0" step="any" style="text-align:center; height: 20px; width: 100px; border: 1px solid #e0e0e0;" name="price" ng-blur="updateSaleServTemp(newservsaletemp)" string-to-number ng-model="newservsaletemp.price" class="form-control form-control-sm mb-1">
                                        @else
                                            @{{newservsaletemp.price | number:2}}
                                        @endif
                                        </td>
                                        <td>@{{(newservsaletemp.price * newservsaletemp.no_of_repeatition) | number:2}}</td>
                                        <td>@{{newservsaletemp.discount * newservsaletemp.no_of_repeatition | number:2}}</td>
                                        @if($settings->is_vat_registered)
                                        <td><select ng-model="newservsaletemp.with_vat" name="sold_in" ng-change="updateSaleServTemp(newservsaletemp)" style="border: 1px solid #e0e0e0;">
                                            <option value="no">{{trans('navmenu.no')}}</option>
                                            <option value="yes">{{trans('navmenu.yes')}}</option>
                                        </select></td>
                                        <td>@{{newservsaletemp.vat_amount | number:2}}</td>
                                        @endif
                                        <td><a href="#" ng-click="removeSaleServTemp(newservsaletemp.id)"><span class="glyphicon glyphicon-trash" aria-hidden="true" style="color: red"></span></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th>{{trans('navmenu.total')}}</th>
                                        <th></th>
                                        <th></th>
                                        <th>@{{sumServ(servsaletempitems) | number:2}}</th>
                                        <th>
                                            <input type="number" step="any" style="text-align:left;" name="serv_sale_discount" ng-blur="updateSaleTempServDiscount(servsaletempitems)" ng-model="total_serv_discount" value="@{{sumServDiscount(servsaletempitems)}}" class="form-control form-control-sm mb-1">
                                        </th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="row">
                                        <input type="hidden" id="no_items" name="no_items" value="@{{saletempitems.length+servsaletempitems.length}}" class="form-control form-control-sm mb-3" readonly>
                                        <div class="col-sm-6" id="amount_paid" style="display: none;">
                                            <label for="total" class="form-label">{{trans('navmenu.amount_paid')}}</label>
                                            <input type="number" min="0" class="form-control form-control-sm mb-3" id="add_payment" string-to-number ng-model="add_payment" placeholder="{{trans('navmenu.hnt_amount_paid')}}" name="amount_paid" />
                                        </div>

                                        @if($settings->inv_no_type == 'Manual')
                                        <div class="col-sm-6" id="invoice_no" style="display: none;">
                                            <label for="total" class="form-label">{{trans('navmenu.invoice_no')}}</label>
                                            <input type="text" class="form-control mb-3" id="invoice" placeholder="Enter Invoice Number" name="inv_no" />
                                        </div> 
                                        @endif
                                        <div class="col-sm-6" id="vehcleno" style="display: none;">     
                                            <label for="total" class="form-label">{{trans('navmenu.vehicle_no')}}</label>
                                            <input type="text" class="form-control form-control-sm mb-3" id="vehicle_no" placeholder="{{trans('navmenu.vehicle_no')}}" name="vehicle_no"/>
                                        </div>
                                        <div class="col-sm-12">
                                            <label for="employee" class="form-label">{{trans('navmenu.comments')}}</label>
                                            <textarea  class="form-control form-control-sm mb-3" name="comments" ng-model="saletemp.comments" id="comments" ></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-6">
                                    <table class="table " style="width: 100%;">
                                        <tr>
                                            <th>{{trans('navmenu.subtotal')}}</th>
                                            <th style="text-align: right;"><b>@{{ (sum(saletempitems)+sumServ(servsaletempitems)) | number:2}}</b></th>
                                        </tr>
                                        <tr>
                                            <th>{{trans('navmenu.total_discount')}}</th>
                                            <th style="text-align: right;"><b>@{{ (sumDiscount(saletempitems)+sumServDiscount(servsaletempitems)) | number:2}}</b></th>
                                        </tr>
                                        <tr>
                                            <th>{{trans('navmenu.vat')}}</th>
                                            <th style="text-align: right;"><b>@{{ (sumVAT(saletempitems)+sumServVAT(servsaletempitems)) | number:2}}</b></th>
                                        </tr>
                                        <tr>
                                            <th>{{trans('navmenu.total')}}</th>
                                            <th style="text-align: right;"><b>@{{ (((sum(saletempitems)+sumServ(servsaletempitems))-(sumDiscount(saletempitems)+sumServDiscount(servsaletempitems)))+(sumVAT(saletempitems)+sumServVAT(servsaletempitems))) | number:2}}</b></th>
                                        </tr>  
                                        <tr>
                                            <th>{{trans('navmenu.currency')}}</th>
                                            <th style="text-align: right;"><b>@{{saletemp.currency}}</b></th>
                                        </tr>
                                    </table>

                                    <div class="row">
                                        <div class="col-sm-4" style="margin-top: 5px;">
                                            <input type="checkbox" id="print_receipt" name="print_receipt">
                                            <label for="print_receipt">Print</label>
                                        </div>
                                        <div class="col-sm-4" style="margin-top: 5px;">
                                            <button type="submit" name="myButton" class="btn btn-success btn-sm">{{trans('navmenu.btn_submit')}}</button>
                                        </div>
                                        
                                        <div class="col-sm-4" style="margin-top: 5px;">
                                            <button onclick="confirmCancel()" type="button" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <form id="delete-form" method="POST" action="{{ route('pos.destroy', encrypt($saletemp->id))}}" style="display: inline;">
                            @csrf
                            @method("DELETE")
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->

    <!-- Modal -->
    <div class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{trans('navmenu.new_customer')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form class="form" method="POST" action="{{url('new-customer')}}">
                <div class="modal-body">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6">
                              <label class="form-label">{{trans('navmenu.customer_name')}} <span style="color: red; font: bold;">*</span></label>
                              <input type="text" name="name" required placeholder="{{ trans('navmenu.hnt_customer_name') }}" class="form-control form-control-sm mb-3">
                        </div>
                        
                        <div class="col-sm-6">
                              <label class="form-label">{{trans('navmenu.phone_number')}}</label>
                              <input type="text" name="phone" placeholder="{{trans('navmenu.hnt_customer_mobile')}}" class="form-control form-control-sm mb-3"  data-inputmask='"mask": "9999999999"' data-mask>
                        </div>
                        
                        <div class="col-sm-6">
                              <label for="register-email" class="form-label">{{trans('navmenu.email_address')}}</label>
                              <input id="register-email" type="text" name="email" placeholder="{{trans('navmenu.hnt_customer_email')}}" class="form-control form-control-sm mb-3">
                        </div>
                        <div class="col-sm-6">
                            <label for="address" class="form-label">{{trans('navmenu.postal_address')}}</label>
                            <input id="address" type="text" name="postal_address" placeholder="{{trans('navmenu.hnt_postal_address')}}" class="form-control form-control-sm mb-3">
                        </div>

                        <div class="col-sm-6">
                            <label for="address" class="form-label">{{trans('navmenu.physical_address')}}</label>
                            <input id="address" type="text" name="physical_address" placeholder="{{trans('navmenu.hnt_physical_address')}}" class="form-control form-control-sm mb-3">
                        </div>

                        <div class="col-sm-6">
                            <label for="address" class="form-label">{{trans('navmenu.street')}}</label>
                            <input id="address" type="text" name="street" placeholder="{{trans('navmenu.hnt_street')}}" class="form-control form-control-sm mb-3">
                        </div>
                        
                        <div class="col-sm-6">
                              <label class="form-label">{{trans('navmenu.tin')}}</label>
                              <input type="text" name="tin" placeholder="{{trans('navmenu.hnt_customer_tin')}}" class="form-control form-control-sm mb-3"  data-inputmask='"mask": "999-999-999"' data-mask>
                        </div>
                        <div class="col-sm-6">
                              <label class="form-label">{{trans('navmenu.vrn')}}</label>
                              <input type="text" name="vrn" placeholder="{{trans('navmenu.hnt_customer_vrn')}}" class="form-control form-control-sm mb-3">
                        </div>
                        <div class="col-sm-6">
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
                        <div class="col-sm-6">
                            <label class="form-label">{{trans('navmenu.id_number')}}</label>
                            <input type="text" name="custid" placeholder="{{trans('navmenu.hnt_id_number')}}" class="form-control form-control-sm mb-3">
                        </div>            
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                    <button type="submit" class="btn btn-primary btn-sm">{{trans('navmenu.btn_save')}}</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="damageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">�</span></button>
                    <h4 class="modal-title" id="myModalLabel">
                    {{trans('navmenu.new_depth_measure')}} </h4>
                </div>
            <form class="form-validate" method="POST" action="{{url('damages')}}">
                <div class="modal-body">
                    @csrf
                    <div class="col-sm-6">
                        <label class="form-label">{{trans('navmenu.product_name')}}</label>
                        <select name="product_id" class="form-control" required>
                            <option value="">{{trans('navmenu.select_product')}}</option>
                            @if(!is_null($products))
                            @foreach($products as $product)
                            <option value="{{$product->id}}">{{$product->name}}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">{{trans('navmenu.depth_measure')}}<span style="color: red;"> *</span></label>
                        <input id="deph_measure" type="number" step="any" name="deph_measure" placeholder="{{trans('navmenu.hnt_enter_quantity')}}" class="form-control">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">{{trans('navmenu.date')}}</label>
                        <select onchange="wegDam(this)" class="form-control">
                            <option value="auto">Auto</option>
                            <option value="manual">Manual</option>
                        </select>
                    </div>
                    <div class="col-sm-6" id="dam_date_field" style="display: none;">
                        <label class="form-label">{{trans('navmenu.pick_date')}}</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" name="dam_date" id="dam_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn btn-success">{{trans('navmenu.btn_save')}}</button>
                    <button type="button" class="btn btn-warning" data-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                </div>
            </form>
            </div>
        </div>
    </div>
@endsection
    
    <?php 
        $is_manager = false;
        if (Auth::user()->hasrole('manager')) {
            $is_manager = true;
        }
    ?>

<link rel="stylesheet" href="{{asset('css/DatePickerX.css')}}">
<script src="{{asset('js/DatePickerX.min.js')}}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="sale_date"]');
            var $max = document.querySelector('[name="due_date"]');
            var $dam = document.querySelector('[name="dam_date"]');
            var $exp = document.querySelector('[name="expire_date"]');

            var isManager = "<?php echo $is_manager; ?>";
            var mind = "<?php echo $settings->sp_mindays; ?>";
            if(isManager){
                mind = "<?php echo $mindays; ?>";
            }
            var d = new Date();
            d.setDate(d.getDate() - mind);
            $min.DatePickerX.init({
                mondayFirst: true,
                minDate    : d,
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                minDate    : new Date(),
                // maxDate    : new Date()
            });

            $dam.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                // minDate    : new Date(),
                maxDate    : new Date()
            });

            $exp.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                minDate    : new Date(),
                // maxDate    : new Date()
            });
        });
    </script>