@extends('layouts.prod')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
<script type="text/javascript" src="../js/rmpurchase.js"></script>
<script>
    function validateform(form) {
        var items = document.rmitemform.no_items.value;
        if (items == 0) {
            // alert('Please select at least one item to continue.');
            Swal.fire(
              'Nothing To Submit!',
              'Please select at least one item to continue.',
              'info')
            
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
            window.location.href="{{url('cancel-rmitem')}}";
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function weg(elem) {
      var x = document.getElementById("rmitem_date_field");
      
      if(elem.value !== "auto") {
        x.style.display = "block";
      } else {
        x.style.display = "none";
        $("#purchase_date").val('');
      }
    }
    
    function wegPurchaseType(elem) {

        var c = document.getElementById('paid-field');
        var ad = document.getElementById('amount_due');
        var acc = document.getElementById('account');

        var sbscr = "<?php echo $shop->subscription_type_id; ?>";

        if (sbscr == 3 || sbscr == 4) {
            var or = document.getElementById('order_no');
            var dn = document.getElementById('delivery_note_no');
            var inv = document.getElementById('invoice_no');
            
            if (elem.value === "credit") {

                acc.style.display = "none";
                or.style.display = "block";
                dn.style.display = "block";                    
                inv.style.display = "block";
                c.style.display = "block";
                
            }else{
                acc.style.display = "block";
                or.style.display = "none";
                dn.style.display = "none";
                inv.style.display = "none";
            }
        }else{
            var paid = document.getElementById('paid-field');
            if (elem.value === "credit") {
                acc.style.display = "none";
                paid.style.display = "block";
            }else{
                acc.style.display = "block";
                paid.style.display = "none";
            }
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
    </div>
    <!--end breadcrumb-->
    <div class="row" ng-controller="SearchItemCtrl" ng-init="rmPurchaseId('<?php echo $rmtemp->id; ?>')">
        <div class="col-xl-3 mx-auto">
            <h6 class="mb-0 text-uppercase">{{trans('navmenu.search_raw_material')}}</h6>
            <hr>
            <div class="card">
                <div class="card-body">
                    <div class="p-2 border rounded"> 
                        <div class="col-sm-12 text-center mb-3">
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#raw_materialModal">
                                <i class="bx bx-plus"></i>
                                {{trans('navmenu.new_raw_material')}}
                            </button>
                        </div>
                        <div class="col-sm-12">
                            <label class="form-label">{{trans('navmenu.search_tap')}}</label> 
                            <input ng-model="searchKeyword" placeholder="{{trans('navmenu.search_raw_material')}}" class="form-control form-control-sm mb-3">
                        </div>
                        <div class="col-sm-12">
                            <ul class="list-group">
                                <li class="list-group-item d-flex justify-content-between align-items-center" ng-repeat="item in items  | filter: searchKeyword | limitTo:10" ng-click="addStockTemp(item, newrmitemtemp , tempid)">
                                    <div class="col-sm-11">
                                        @{{item.name}}
                                    </div>
                                    <div class="col-sm-1">
                                        <span class="badge bg-success rounded-pill"><span class="bx bx-redo" aria-hidden="true"></span></span>
                                    </div>
                                </li>
                            </ul>
                        </div>  
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-9 mx-auto">
            <h6 class="mb-0 text-uppercase">{{trans('navmenu.purchase_items')}}</h6>
            <hr>
            <div class="card">
                <div class="card-body">
                    <button type="button" class="btn btn-success btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#supplierModal"><i class="bx bx-user-plus"></i>{{trans('navmenu.new_supplier')}}</button>
                    <div class="p-4 border rounded">
                        <form class="row g-3"  name="rmitemform" method="POST" action="{{route('rm-purchases.store')}}" onsubmit="return validateform(this)" ng-if="rmtemp">
                            @csrf
                            <input type="hidden" name="rm_purchase_temp_id" placeholder="" value="{{$rmtemp->id}}" class="form-control form-control-sm mb-3">
                            <div class="col-sm-6">
                                <label for="suppler_id" class="form-label">{{trans('navmenu.supplier')}}</label>
                                <select name="supplier_id" id="supplier_id"  class="form-select form-select-sm mb-3" ng-model="rmtemp.supplier_id" ng-change="updateRmTempInfo(rmtemp)" ng-options="supplier.id as supplier.name for supplier in suppliers">
                                    <option value="">{{trans('navmenu.unknown')}}</option>
                                </select>
                            </div>

                            <div class="col-sm-3">
                                <label for="date_set" class="form-label">{{trans('navmenu.purchase_date')}}</label>
                                <select name="date_set" id="date_set" ng-model="rmtemp.date_set" ng-change="updateRmTempInfo(rmtemp)" onchange="weg(this)" class="form-select form-select-sm mb-3">
                                    <option value="auto">Auto</option>
                                    <option value="manual">Manual</option>
                                </select>
                            </div>

                            <div class="col-sm-3" id="rmitem_date_field"  style="display : none;">
                                <label class="form-label">{{trans('navmenu.purchase_date')}}</label>
                                <div class="inner-addon left-addon">
                                    <i class="myaddon bx bx-calendar"></i>
                                    <input type="text" name="date" id="purchase_date" ng-model="rmtemp.date"  placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-3" ng-change="updateRmTempInfo(rmtemp)">
                                </div>
                            </div>

                            <div class="col-sm-3" id="purchase_type_field">
                                <label class="form-label">{{trans('navmenu.purchase_type')}}</label>
                                <select name="purchase_type" id="purchase_type" onchange="wegPurchaseType(this)" ng-model="rmtemp.purchase_type" ng-change="updateRmTempInfo(rmtemp)"  class="form-select form-select-sm mb-3" required>
                                     <option value="">{{trans('navmenu.select_purchase_type')}}</option>
                                    <option value="cash">{{trans('navmenu.cash_purchases')}}</option>
                                    <option value="credit">{{trans('navmenu.credit_purchases')}}</option>
                                </select>
                            </div>
                                    
                            <div class="col-sm-3" id="account" style="display: none;">
                                <label for="payment_type" class="form-label">{{trans('navmenu.pay_method')}}</label>
                                <select class="form-select form-select-sm mb-3" ng-model="rmtemp.pay_type" ng-change="updateRmTempInfo(rmtemp)" name="account"  required>
                                    <option value="Cash">{{trans('navmenu.cash')}}</option>
                                    <option value="Cheque">{{trans('navmenu.cheque')}}</option>
                                    <option value="Bank">{{trans('navmenu.bank')}}</option>
                                    <option value="Mobile Money">{{trans('navmenu.mobilemoney')}}</option>
                                </select>
                            </div>

                          {{--  <!-- <div id="bankdetail" style="display: none;">
                                <div class="col-sm-4" id="deposit_mode" style="display: none;">
                                    <label class="form-label">Deposit Mode</label>
                                    <select name="deposit_mode" class="form-select form-select-sm mb-3">
                                        <option>Direct Deposit</option>
                                        <option>Bank Transfer</option>
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label">Bank Name </label>
                                    <select name="bank_name" class="form-select form-select-sm mb-3">
                                        <option value="">Select Bank Account</option>
                                        @foreach($bdetails as $detail)
                                        <option value="{{$detail->id}}">{{$detail->bank_name}} - {{$detail->branch_name}}</option>
                                        @endforeach
                                    </select>
                                </div> 

                                <div class="col-sm-4" id="cheque" style="display: none;">
                                    <label class="form-label">Cheque Number</label>
                                    <input id="name" type="text" name="cheque_no" placeholder="Please enter Cheque Number" class="form-control mb-3">
                                </div>

                                <div class="col-sm-4" id="expire" style="display: none;">
                                    <label class="form-label">Expire Date</label>
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div> 
                                        <input id="name" type="text" name="expire_date" placeholder="Please enter Expire Date" class="form-control mb-3">
                                    </div>
                                </div>

                                <div class="col-sm-4" id="slip" style="display: none;">
                                    <label class="form-label">Credit Card/Bank Slip Number</label>
                                    <input id="name" type="text" name="slip_no" placeholder="Please enter Credit Card/Bank Slip number" class="form-control mb-3">
                                </div>
                            </div> 
                            <div id="mobaccount" style="display: none;">
                                <div class="col-sm-4">
                                    <label class="form-label">Mobile Money Operator </label>
                                    <select class="form-select form-select-sm mb-3" name="operator">
                                        <option value="">Select Operator</option>
                                        <option>AirtelMoney</option>
                                        <option>EzyPesa</option>
                                        <option>M-Pesa</option>
                                        <option>TigoPesa</option>
                                        <option>HaloPesa</option>
                                    </select>
                                </div>
                            </div> -->

                        --}}

                            @if($shop->subscription_type_id >= 3)
                            <div class="col-sm-3">
                                <div class="form-group" id="order_no" style="display: none;">
                                    <label for="total" class="form-label">{{trans('navmenu.purchase_order_no')}}</label>
                                    <input type="text" class="form-control form-control-sm mb-3" id="ord_no" ng-model="rmtemp.order_no" ng-blur="updateRmTempInfo(rmtemp)" placeholder="{{trans('navmenu.hnt_order_no')}}" name="order_no" />
                                </div> 
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group" id="delivery_note_no" style="display: none;">
                                    <label for="total" class="form-label">{{trans('navmenu.delivery_note_no')}}</label>
                                    <input type="text" class="form-control form-control-sm mb-3" id="dn_no" ng-model="rmtemp.delivery_note_no" ng-blur="updateRmTempInfo(rmtemp)" placeholder="{{trans('navmenu.hnt_delivery_note_no')}}" name="delivery_note_no" />
                                </div> 
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group" id="invoice_no" style="display: none;">
                                    <label for="total" class="form-label">{{trans('navmenu.invoice_no')}}</label>
                                    <input type="text" ng-model="rmtemp.invoice_no" ng-blur="updateRmTempInfo(rmtemp)" class="form-control form-control-sm mb-3" id="inv_no" placeholder="{{trans('navmenu.hnt_invoice_no')}}" name="invoice_no" />
                                </div> 
                            </div>
                            @endif

                            @if($settings->allow_multi_currency)
                            <div class="row">
                                <div class="col-sm-3">
                                    <label class="form-label">{{trans('navmenu.currency')}}</label>
                                    <select name="currency" id="currency" class="form-select form-select-sm mb-3" ng-model="rmtemp.currency" ng-change="updateRmTempInfo(rmtemp)" ng-options="curr.code as curr.code for curr in currencies" required>
                                    </select>
                                </div>
                                <div class="col-sm-3" ng-if="rmtemp.currency != rmtemp.defcurr">
                                    <label class="form-label">Exchange Rate Mode</label>
                                    <select name="ex_rate_mode"  class="form-select form-select-sm mb-3" ng-model="rmtemp.ex_rate_mode">
                                        <option value="Locale" selected>1 @{{rmtemp.defcurr}} Equals ? @{{rmtemp.currency}}</option>
                                        <option value="Foreign">1 @{{rmtemp.currency}} Equals ? @{{rmtemp.defcurr}}</option>
                                    </select>
                                </div>
                                <div class="col-sm-3" ng-if="rmtemp.currency != rmtemp.defcurr && rmtemp.ex_rate_mode == 'Locale'">
                                    <label class="form-label">Rate Amount in @{{rmtemp.currency}}</label>
                                    <input id="foreign-ex-rate" type="number" min="0" step="any" name="foreign_ex_rate" class="form-control form-control-sm mb-3" string-to-number ng-model="rmtemp.foreign_ex_rate" ng-blur="updateRmTempInfo(rmtemp)">
                                </div>
                                <div class="col-sm-3" ng-if="rmtemp.currency != rmtemp.defcurr && rmtemp.ex_rate_mode == 'Foreign'">
                                    <label class="form-label">Rate Amount in @{{rmtemp.defcurr}}</label>
                                    <input id="local-ex-rate" type="number" min="0" step="any" name="local_ex_rate" class="form-control form-control-sm mb-3" string-to-number ng-model="rmtemp.local_ex_rate" ng-blur="updateRmTempInfo(rmtemp)">
                                </div>
                            </div>
                            @endif
                            
                            <div class="col-sm-12">
                                <table class="table table-responsive table-striped display nowrap" style="width: 100%; display: block; overflow: scroll; overflow: auto;">
                                <tr>
                                    <th style="text-align: center;">#</th>
                                    <th style="text-align: center;">{{trans('navmenu.material_name')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                    <th style="text-align: center;">&nbsp;</th>
                                </tr>
                                <tr ng-repeat="newrmitemtemp in rmitemtemp" id="temps">
                                    <td>@{{$index + 1}}</td>
                                    <td>@{{newrmitemtemp.name}}</td>
                                    <td><input type="number" name="qty" ng-blur="updateStockTemp(newrmitemtemp)" string-to-number ng-model="newrmitemtemp.qty" min="0" step="any" value="@{{newrmitemtemp.qty}}" style="text-align:center; width: 140px;" autocomplete="off" class="form-control form-control-sm"></td>
                                    <td><input type="number" name="unit_cost"  ng-blur="updateStockTemp(newrmitemtemp)" ng-model="newrmitemtemp.unit_cost" min="0" step="any" value="@{{(newrmitemtemp.unit_cost )}}" style="text-align:center; width : 140px;"  class="form-control form-control-sm"></td>
                                    
                                    <td><input type="number"   name="total" ng-blur="updateStockTemp(newrmitemtemp)" ng-model="newrmitemtemp.total" min="0" step="any" value="@{{newrmitemtemp.total}}" style="text-align:center; width: 140px;" autocomplete="off" class="form-control form-control-sm" readonly></td>
                                    
                                    <td><a href="#" ng-click="removeStockTemp(newrmitemtemp.id)"><span class="bx bx-trash" aria-hidden="true" style="color: red"></span></a>
                                    </td>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th style="text-align : center;">{{trans('navmenu.total')}}</td>
                                    <th style="text-align : center;"><b>@{{sum(rmitemtemp) * rmtemp.ex_rate | number:2}}</b></td>
                                    <th></th>
                                </tr>
                            </table>
                        </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="row">
                                        @if($shop->subscription_type_id == 1)
                                        <div class="col-sm-12" id="amount_paid" style="display: none;">
                                            <label for="total" class="form-label">{{trans('navmenu.amount_paid')}}</label>
                                            <input type="number" min="0" class="form-control form-control-sm mb-3" id="add_payment" string-to-number ng-model="add_payment" placeholder="{{trans('navmenu.hnt_amount_paid')}}" name="amount_paid" />
                                        </div>
                                        @endif 
                                        
                                        <div class="col-sm-12">
                                            <label for="comments" class="form-label">{{trans('navmenu.comments')}}</label>
                                            <textarea  class="form-control form-control-sm mb-3" name="comments" id="comments" ></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 pt-3">
                                    <button type="submit" name="myButton" class="btn btn-success btn-sm mb-3">{{trans('navmenu.btn_submit')}}</button>
                                    <button onclick="confirmCancel()" type="button" class="btn btn-warning btn-sm mb-3">{{trans('navmenu.btn_cancel')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->

        <!-- Modal -->
<div class="modal fade" id="supplierModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">New Supplier</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="form-validate" method="POST" action="{{route('suppliers.store')}}">
                <div class="modal-body row">
                    @csrf
                    <input type="hidden" name="supplier_for" value="Raw Materials">
                    <div class="col-md-6">
                          <label class="form-label">Supplier Name</label>
                          <input id="register-username" type="text" name="name" required placeholder="Please enter supplier name" class="form-control form-control-sm mb-3">
                    </div>
                    
                    <div class="col-md-6">
                          <label class="form-label">Phone number</label>
                          <input id="register-username" type="text" name="contact_no" placeholder="Please enter supplier mobile number" class="form-control form-control-sm mb-3">
                    </div>
                    
                    <div class="col-md-6">
                          <label class="form-label">Email Address</label>
                          <input id="register-email" type="text" name="email" placeholder="Please enter supplier email address" class="form-control form-control-sm mb-3">
                    </div>
                    <div class="col-md-6">
                          <label class="form-label">Address</label>
                          <input id="address" type="text" name="address" placeholder="Please enter supplier address" class="form-control form-control-sm mb-3">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm">Save</button>
                    <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Cancel</button>
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

    <link rel="stylesheet" href="{{ asset('css/DatePickerX.css') }}">
    <script src="{{ asset('js/DatePickerX.min.js') }}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[id="purchase_date"]');
            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>