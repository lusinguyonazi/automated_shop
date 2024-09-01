@extends('layouts.app')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/stockentries.js')}}"></script>
    <script>
        function validateform(form) {
            var items = document.stockform.no_items.value;
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

        function confirmCancel(id) {
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
                window.location.href="{{url('cancel-purchase')}}/"+id;
                Swal.fire(
                  "{{trans('navmenu.deleted')}}",
                  "{{trans('navmenu.cancelled')}}",
                  'success'
                )
              }
            })
        }

        function weg(elem) {
          var x = document.getElementById("stock_date_field");
          if(elem.value !== "auto") {
            x.style.display = "block";
          } else {
            x.style.display = "none";
            $("#stock_date").val('');
          }
        }

        function wegPurchaseType(elem) {
            var paid = document.getElementById('paid-field');
            var ad = document.getElementById('amount_due');
            var acc = document.getElementById('account');

            var sbscr = "<?php echo $shop->subscription_type_id; ?>";
            if (sbscr >= 3) {
                var or = document.getElementById('order_no');
                var dn = document.getElementById('delivery_note_no');
                var inv = document.getElementById('invoice_no');
                if (elem.value === "credit") {
                    acc.style.display = "none";
                    or.style.display = "block";
                    dn.style.display = "block";                    
                    inv.style.display = " block";
                }else{
                    acc.style.display = "block";
                    or.style.display = "none";
                    dn.style.display = "none";
                    inv.style.display = "none";
                }
            }else{
                if (elem.value === "credit") {
                    acc.style.display = "none";
                    paid.style.display = "block";
                }else{
                    acc.style.display = "block";
                    paid.style.display = "none";
                }
            }
        }

        function submitTemp(index) {
            document.getElementById('ptemp-form-'+index).submit();
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
    <div class="row" ng-controller="SearchItemCtrl" ng-init="purchaseTempId('<?php echo $purchasetemp->id; ?>')">
        <div class="col-md-3 mx-auto">
            <h6 class="mb-0 text-uppercase">{{trans('navmenu.search_product')}}</h6>
            <hr>
            <div class="card radius-6">
                <div class="card-body">
                    <div class="col-sm-12 mb-3 text-center" >
                        <button type="button" class="btn btn-primary btn-sm"   data-bs-toggle="modal" data-bs-target="#productModal">
                            <i class="bx bx-plus mr-1"></i>
                            {{trans('navmenu.new_product')}}
                        </button>
                    </div>
                    <div class="col-sm-12">
                        <label class="form-label">Scan Barcode</label>
                        <input id="scanner_input_purchase" name="barcode" type="text" ng-model="barcode" class="form-control form-control-sm mb-3" placeholder="Scan barcode from an item ..." type="text" autofocus/>
                    </div>
                    <div class="col-sm-12">
                        <label class="form-label">{{trans('navmenu.search_product')}}</label> 
                        <input ng-model="searchKeyword" placeholder="{{trans('navmenu.search_product')}}" class="form-control form-control-sm mb-3">
                    </div>  
                    <div class="col-sm-12">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center" ng-repeat="item in items  | filter: searchKeyword | limitTo:10" ng-click="addStockTemp(item, newstocktemp, tempid)">
                                <div class="col-sm-11">
                                    @{{item.name}}
                                    <br><small style="color: #757575;">@{{item.product_no}}</small>
                                </div>
                                <div class="col-sm-1">
                                    <span class="badge bg-success rounded-pill"><span class="bx bx-redo" aria-hidden="true"></span></span>
                                </div>
                            </li>
                        </ul>
                    </div>  
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <div class="col-xl-9 mx-auto">
            <h6 class="mb-0 text-uppercase">{{trans('navmenu.purchase_items')}}</h6>
            <hr>
            <div class=" card radius-6">
                <!-- /.box-header -->
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-6 d-lg-flex align-items-center mb-1 gap-1">
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#supplierModal"><i class="bx bx-user-plus"></i>{{trans('navmenu.new_supplier')}}</button>
                        </div>
                        <div class="btn-group col-sm-6" role="group">
                            <button type="button" class="btn btn-outline-danger btn-sm">{{$pendingtemps->count()}}</button>
                            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="dropdown">Pending Bills/Invoices <i class="bx bx-caret-down"></i></button>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end"> 
                                @foreach($pendingtemps as $key => $temp) 
                                <form class="row g-3" method="POST" action="{{'pt-purchase'}}" id="ptemp-form-{{$key}}">
                                    @csrf
                                    <input type="hidden" name="id" value="{{$temp->id}}">
                                    <a class="dropdown-item" href="javascript:;" onclick="submitTemp('<?php echo $key; ?>')">{{$temp->name}} (<span class="badge rounded-pill bg-warning text-dark"> Created since {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $temp->created_at)->diffForHumans() }}</span>)</a>
                                </form>  
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="p-4 border rounded">
                        <form class="row g-3 needs-validation" novalidate name="stockform" method="POST" action="{{route('purchases.store')}}" onsubmit="return validateform(this)" ng-if="purchasetemp">
                            @csrf
                            <input type="hidden" name="purchase_temp_id" placeholder="" value="{{$purchasetemp->id}}" class="form-control form-control-sm mb-3">
                            <div class="col-sm-3">
                                <label for="supplier_id" class="form-label">{{trans('navmenu.supplier')}}</label>
                                <select name="supplier_id" id="supplier_id" required class="form-select form-select-sm mb-3" ng-model="purchasetemp.supplier_id" ng-change="updatePurchaseTempInfo(purchasetemp)" ng-options="supplier.id as supplier.name for supplier in suppliers">
                                    <option value="">{{trans('navmenu.unknown')}}</option>
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <label for="date_set" class="form-label">{{trans('navmenu.purchase_date')}}</label>
                                <select name="date_set" id="date_set" ng-model="purchasetemp.date_set" ng-change="updatePurchaseTempInfo(purchasetemp)" onchange="weg(this)" class="form-select form-select-sm mb-3">
                                    <option value="auto">Auto</option>
                                    <option value="manaul">Manual</option>
                                </select>
                            </div>
                            <div class="col-sm-3" id="stock_date_field" style="display: none;">
                                <label class="form-label">{{trans('navmenu.purchase_date')}}</label>
                                <div class="inner-addon left-addon">
                                    <i class="myaddon bx bx-calendar"></i>
                                    <input type="text" name="purchase_date" id="purchase_date" ng-model="purchasetemp.purchase_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-3">
                                </div>
                            </div>
                            <div class="col-sm-3" id="purchase_type_field">
                                <label class="form-label">{{trans('navmenu.purchase_type')}}</label>
                                <select name="purchase_type" id="purchase_type" ng-model="purchasetemp.purchase_type" ng-change="updatePurchaseTempInfo(purchasetemp)" onchange="wegPurchaseType(this)" class="form-select form-select-sm mb3" required>
                                    <option value="">{{trans('navmenu.select_purchase_type')}}</option>
                                    <option value="cash">{{trans('navmenu.cash_purchases')}}</option>
                                    <option value="credit">{{trans('navmenu.credit_purchases')}}</option>
                                </select>
                            </div>

                            <div class="col-md-3" id="account" style="display: none;">
                                <label for="account" class="form-label">{{trans('navmenu.paid_from')}} <span  style="color: red; font-weight: bold;">*</span></label>
                                <select class="form-select form-select-sm mb3" name="account" required>
                                    <option value="Cash">{{trans('navmenu.cash')}}</option>
                                    <option value="Bank">{{trans('navmenu.bank')}}</option>
                                    <option value="Mobile Money">{{trans('navmenu.mobilemoney')}}</option>
                                </select>
                            </div>
                            @if($settings->allow_multi_currency)
                            <div class="col-sm-3">
                                <label class="form-label">{{trans('navmenu.currency')}}</label>
                                <select name="currency" id="currency" class="form-select form-select-sm mb-3" ng-model="purchasetemp.currency" ng-change="updatePurchaseTempInfo(purchasetemp)" ng-options="curr.code as curr.code for curr in currencies" required>
                                </select>
                            </div>
                            <div class="col-sm-3" ng-if="purchasetemp.currency != purchasetemp.defcurr">
                                <label class="form-label">Exchange Rate Mode</label>
                                <select name="ex_rate_mode"  class="form-select form-select-sm mb-3" ng-model="purchasetemp.ex_rate_mode">
                                    <option value="Locale" selected>1 @{{purchasetemp.defcurr}} Equals ? @{{purchasetemp.currency}}</option>
                                    <option value="Foreign">1 @{{purchasetemp.currency}} Equals ? @{{purchasetemp.defcurr}}</option>
                                </select>
                            </div>
                            <div class="col-sm-3" ng-if="purchasetemp.currency != purchasetemp.defcurr && purchasetemp.ex_rate_mode == 'Locale'">
                                <label class="form-label">Rate Amount in @{{purchasetemp.currency}}</label>
                                <input id="foreign-ex-rate" type="number" min="0" step="any" name="foreign_ex_rate" class="form-control form-control-sm mb-3" string-to-number ng-model="purchasetemp.foreign_ex_rate" ng-blur="updatePurchaseTempInfo(purchasetemp)">
                            </div>
                            <div class="col-sm-3" ng-if="purchasetemp.currency != purchasetemp.defcurr && purchasetemp.ex_rate_mode == 'Foreign'">
                                <label class="form-label">Rate Amount in @{{purchasetemp.defcurr}}</label>
                                <input id="local-ex-rate" type="number" min="0" step="any" name="local_ex_rate" class="form-control form-control-sm mb-3" string-to-number ng-model="purchasetemp.local_ex_rate" ng-blur="updatePurchaseTempInfo(purchasetemp)">
                            </div>
                            @endif
                            
                            @if($shop->subscription_type_id >= 3)

                            <div class="col-md-3">
                                <div id="order_no" style="display: none;">
                                    <label for="total" class="form-label">{{trans('navmenu.purchase_order_no')}}</label>
                                    <input type="text" class="form-control form-control-sm mb-3" id="ord_no" placeholder="{{trans('navmenu.hnt_order_no')}}" name="order_no" />
                                </div> 
                            </div>

                            <div class="col-md-3">
                                <div id="delivery_note_no" style="display: none;">
                                    <label for="total" class="form-label">{{trans('navmenu.delivery_note_no')}}</label>
                                    <input type="text" class="form-control form-control-sm mb-3" id="dn_no" placeholder="{{trans('navmenu.hnt_delivery_note_no')}}" name="delivery_note_no" />
                                </div> 
                            </div>

                            <div class="col-md-3">
                                <div  id="invoice_no" style="display: none;">
                                    <label for="total" class="form-label">{{trans('navmenu.invoice_no')}}</label>
                                    <input type="text"  class="form-control form-control-sm mb-3" id="inv_no" placeholder="{{trans('navmenu.hnt_invoice_no')}}" name="invoice_no" />
                                </div> 
                            </div>
                            @endif

                            <div class="col-md-12">
                                <table class="table table-responsive table-striped display nowrap" style="width: 100%; display: block; overflow: scroll; overflow: auto;">
                                    <tr>
                                        <th style="text-align: center;">#</th>
                                        <th style="text-align: center;">{{trans('navmenu.product_name')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                        @if($shop->business_type_id != 1)
                                        <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                        @endif
                                        <th style="text-align: center;">{{trans('navmenu.selling_price')}}</th>
                                        @if($settings->enable_exp_date)
                                        <th style="text-align: center;">{{trans('navmenu.expire_date')}}</th>
                                        @endif
                                        <th style="text-align: center;">&nbsp;</th>
                                    </tr>
                                    <tr ng-repeat="newstocktemp in stocktempitems" id="temps">
                                        <td>@{{$index + 1}}</td>
                                        <td>@{{newstocktemp.name}}</td>
                                        <td><input type="number" name="quantity_in" ng-blur="updateStockTemp(newstocktemp)" ng-model="newstocktemp.quantity_in" min="0" step="any" style="text-align:center; height: 20px; width: 140px; border: 1px solid #e0e0e0;" autocomplete="off"></td>

                                        @if($shop->business_type_id != 1)
                                        <td><input type="number" name="buying_per_unit" ng-blur="updateStockTemp(newstocktemp)" string-to-number ng-model="newstocktemp.buying_per_unit" min="0" step="any" style="text-align:center;height: 20px; width: 140px; border: 1px solid #e0e0e0;" autocomplete="off"></td>
                                        <td><input type="number" name="total" ng-blur="updateStockTemp(newstocktemp)" string-to-number ng-model="newstocktemp.total" min="0" step="any" style="text-align:center; height: 20px; width: 160px; border: 1px solid #e0e0e0;" autocomplete="off"></td>
                                        @endif
                                        <td><input type="number" name="price_per_unit" ng-blur="updateStockTemp(newstocktemp)" string-to-number ng-model="newstocktemp.price_per_unit" min="0" step="any" style="text-align:center;height: 20px; width: 140px; border: 1px solid #e0e0e0;" autocomplete="off"></td>

                                        @if($settings->enable_exp_date)
                                        <td><input type="text" name="expire_date" ng-blur="updateStockTemp(newstocktemp)" ng-model="newstocktemp.expire_date" value="@{{newstocktemp.expire_date}}" style="text-align:center; height: 20px; width: 120px; border: 1px solid #e0e0e0;" autocomplete="off" class="form-control" placeholder="yyyy-mm-dd" onkeyup="
                                            var v = this.value;
                                            if (v.match(/^\d{4}$/) !== null) {
                                                this.value = v + '-';
                                            } else if (v.match(/^\d{4}\-\d{2}$/) !== null) {
                                                this.value = v + '-';
                                            }"
                                        maxlength="10"></td>
                                        @endif
                                        <td><a href="#" ng-click="removeStockTemp(newstocktemp.id)"><span class="bx bx-trash" aria-hidden="true" style="color: red"></span></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        @if($shop->business_type_id != 1)
                                        <th style="text-align: center;"><b>{{trans('navmenu.total')}} (@{{purchasetemp.currency}})</b></th>
                                        <th style="text-align: center;"><b>@{{sum(stocktempitems) | number:2}}</b></th>
                                        @endif
                                        <th></th>
                                        @if($settings->enable_exp_date)
                                        <th></th>
                                        @endif
                                        <th></th>
                                    </tr>
                                </table>
                            </div>

                            <div class="row">
                                @if($shop->subscription_type_id == 1)
                                <div class="col-sm-6" id="paid-field" style="display: none;">
                                    <label for="total" class="form-label">{{trans('navmenu.amount_paid')}}</label>
                                    <input type="number" min="0" class="form-control form-control-sm mb-3" id="add_payment" ng-model="add_payment" placeholder="{{trans('navmenu.hnt_amount_paid')}}" name="amount_paid" />
                                </div>
                                @endif
                                <div class="col-sm-6">
                                    <label for="comments" class="col-sm-3 form-label">{{trans('navmenu.comments')}}</label>
                                    <textarea  class="form-control form-control-sm mb-3" name="comments" id="comments" ng-model="purchasetemp.comments"></textarea>
                                </div>
                                <div class="col-sm-12">
                                    <button onclick="confirmCancel('<?php echo encrypt($purchasetemp->id); ?>')" type="button" class="btn btn-warning btn-sm float-end" style="margin-left: 5px;">{{trans('navmenu.btn_cancel')}}</button>
                                    <button type="submit" name="myButton" class="btn btn-success btn-sm float-end">{{trans('navmenu.btn_submit')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
        </div>
        </div>      
    </div>

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
                    <input type="hidden" name="supplier_for" value="Stock">
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
                    <button type="submit" class="btn btn btn-success">Save</button>
                    <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">{{trans('navmenu.new_product')}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>   
            </div>
            <form class="form-validate" method="POST" action="{{ route('products.store')}}">
                <div class="modal-body row">
                    @csrf
                    <input type="hidden" name="from-purch" value="1">
                    <div class="col-md-6">
                        <label class="form-label">{{trans('navmenu.product_name')}} <span style="color: red; font-weight: bold;">*</span></label>
                        <input id="name" type="text" name="name" required placeholder="{{trans('navmenu.hnt_product_name')}}" class="form-control form-control-sm mb-1">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{trans('navmenu.basic_unit')}} <span style="color: red; font-weight: bold;">*</span></label>
                        <select class="form-select form-select-sm mb-1" name="basic_unit" required style="width: 100%;">
                            @foreach($units as $key => $unit)
                            <option value="{{$key}}">{{$unit}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{trans('navmenu.product_no')}}</label>
                        <input id="name" type="text" name="product_no" placeholder="{{trans('navmenu.hnt_product_no')}}" class="form-control form-control-sm mb-1">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{trans('navmenu.location')}}</label>
                        <input id="location" type="text" name="location" placeholder="{{trans('navmenu.hnt_location')}} (Optional)" class="form-control form-control-sm mb-1">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{trans('navmenu.selling_per_unit')}}</label>
                        <input id="unit_price" type="number" min="0" name="price_per_unit" placeholder="{{trans('navmenu.hnt_selling_price')}}" class="form-control form-control-sm mb-1">
                    </div>
                </div>                    
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm">Save</button>
                    <button type="button" data-bs-dismiss="modal" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</button>
                    {{-- <button type="button" class="btn btn-warning btn-sm" data-dismiss="modal">Cancel</button> --}}
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
        var $min = document.querySelector('[name="stock_date"]');
        var isManager = "<?php echo $is_manager; ?>";
        var mind = 3;
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
    });
</script>