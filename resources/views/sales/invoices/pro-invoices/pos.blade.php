@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
<script type="text/javascript" src="{{asset('js/invpos/pos.js')}}"></script>
@if(Session::has('code'))
<script type="text/javascript">

    $(document).ready(function(){

        $("#myModal").modal('show');

        $('#myModal').on('hidden.bs.modal', function () {
            closeFunction();
        });
    });
</script>
@endif 
<script>
    function validateform(form) {
        var items = document.invoiceform.no_items.value;
        if (items == 0) {
            alert('Please select at least one item to continue.');
            return false;
        }

        if (document.invoiceform.due_date.value === '') {
            Swal.fire(
              'Due Date Required!',
              'Please set invoice Due date.',
              'info'
            )
            return false;
        }

        form.myButton.disabled = true;
        form.myButton.value = "Please wait...";
        return true;
        
    }


    function weg(elem) {
      var x = document.getElementById("invoice_date_field");
      if(elem.value !== "auto") {
        x.style.display = "block";
      } else {
        x.style.display = "none";
        $("#invoice_date").val('');
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
                    <li class="breadcrumb-item active" aria-current="page">{{$title}}</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row" ng-controller="SearchItemCtrl">
        <div class="col-md-3 mx-auto">
            <span class="mb-0 text-uppercase text-center">Products</span>
            <hr>
            <div class="card radius-6">
                <div class="card-body">
                    <div class="col-sm-12">
                        <label class="form-label">Search Product</label>
                        <input ng-model="searchKeyword" placeholder="{{trans('navmenu.search_product')}}" class="form-control form-control-sm mb-3">
                    </div>
                    <div class="col-sm-12">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center" ng-repeat="item in items  | filter: searchKeyword | limitTo:10" ng-click="addSaleTemp(item, newinvoicetemp)">
                                <div class="col-sm-11">
                                    @{{item.name}}<br><small style="color: #757575;">@{{item.product_no}}</small>
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
            <span class="mb-0 text-uppercase text-center">Order items</span>
            <hr>
            <div class="card radius-6">
                <div class="card-header">
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#customerModal"><i class="bx bx-plus"></i>New Customer
                            </button>
                </div>
                <div class="card-body">
                    <form class="row g-3" name="invoiceform" method="POST" action="{{route('pro-invoices.store')}}" onsubmit="return validateform(this)">
                        @csrf
 
                        <div class="col-sm-4">
                            <label for="customer_id" class="form-label">Customer</label>
                            <select name="customer_id" required class="form-control select2">
                                 @foreach($customers as $key => $customer)
                                 <option value="{{$key}}">{{$customer}}</option>
                                 @endforeach
                             </select>
                        </div>
                        <div class="col-sm-2">
                            <label for="invoice" class="form-label">Invoice Date</label>
                            <select name="date_set" id="date_set" onchange="weg(this)" class="form-select form-select-sm mb-3">
                                <option value="auto">Auto</option>
                                <option value="manaul">Manual</option>
                            </select>
                        </div>
                        <div class="col-sm-3" id="invoice_date_field" style="display: none;">
                            <label class="form-label">Invoice Date</label>
                            <div class="inner-addon left-addon">
                                <i class="myaddon bx bx-calendar"></i>
                                <input type="text" name="invoice_date" id="invoice_date" placeholder="Choose Sale date" class="form-control form-control-sm mb-3">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Due date <span style="color: red; font-weight: bold;">*</span></label>
                            <div class="inner-addon left-addon">
                                <i class="myaddon bx bx-calendar"></i>
                                <input type="text" name="due_date" placeholder="Choose Due date" class="form-control form-control-sm mb-3" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <table class="table table-responsive table-striped display nowrap" style="width: 100%; display: block; overflow: scroll; overflow: auto;">
                                <tr>
                                    <th>#</th>
                                    <th>Item name</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Total</th>
                                     @if($settings->is_vat_registered)
                                    <th style="text-align: center;">{{trans('navmenu.add_vat')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.vat')}}</th>
                                    @endif
                                    <th>&nbsp;</th>
                                </tr>
                                <tr ng-repeat="newinvoicetemp in invoicetemp" id="temps">
                                    <td>@{{$index + 1}}</td>
                                    <td>@{{newinvoicetemp.product.name}}</td>
                                    <td><input type="number" style="text-align:center" autocomplete="off" name="quantity" ng-blur="updateSaleTemp(newinvoicetemp)" string-to-number ng-model="newinvoicetemp.quantity" min="0" step="0.25" max="@{{newinvoicetemp.curr_stock}}" value="@{{newinvoicetemp.quantity}}" class="form-control form-control-sm mb-1"></td>
    
                                    <td><input type="number" style="text-align:center" autocomplete="off" name="cost_per_unit" ng-blur="updateSaleTemp(newinvoicetemp)" ng-model="newinvoicetemp.cost_per_unit" value="@{{newinvoicetemp.cost_per_unit}}" class="form-control form-control-sm mb-1"></td>
                                    <td>@{{(newinvoicetemp.cost_per_unit * newinvoicetemp.quantity) | number:0}}</td>
                                    @if($settings->is_vat_registered)
                                    <td ng-if="newinvoicetemp.with_vat == 'no'"><select ng-model="newinvoicetemp.with_vat" name="with_vat" ng-change="updateSaleTemp(newinvoicetemp)" style="border: 1px solid #e0e0e0;">
                                        <option value="no" selected>{{trans('navmenu.no')}}</option>
                                        <option value="yes">{{trans('navmenu.yes')}}</option>
                                    </select></td>
                                    <td ng-if="newinvoicetemp.with_vat == 'yes'"><select ng-model="newinvoicetemp.with_vat" name="with_vat" ng-change="updateSaleTemp(newinvoicetemp)" style="border: 1px solid #e0e0e0;">
                                        <option value="yes" selected>{{trans('navmenu.yes')}}</option>
                                        <option value="no">{{trans('navmenu.no')}}</option>
                                    </select></td>
                                    <td ng-model="newinvoicetemp.vat_amount">@{{newinvoicetemp.vat_amount | number:2}}</td>
                                    @endif
                                    <td><a href="#" ng-click="removeSaleTemp(newinvoicetemp.id)"><span class="bx bx-trash" aria-hidden="true" style="color: red"></span></a>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Notice</label>
                                <textarea  class="form-control form-control-sm mb-3" name="notice" id="notice" ></textarea>
                            </div>

                            <div class="col-md-6">
                                <table class="table table-striped" style="width: 100%;">
                                    <tbody>
                                        <tr>
                                            <td>Subtotal</td>
                                            <td><b>@{{sum(invoicetemp) | number:0}}</b></td>
                                        </tr>
                                        <tr>
                                            <td>Discount</td>
                                            <td><input type="number" style="text-align:left;" name="discount" ng-model="discount" min="0" value="0" id="discount" class="form-control form-control-sm mb-1"></td>
                                        </tr>
                                        <tr>
                                            <td>Shipping Cost</td>
                                            <td><input type="number" style="text-align:left;" name="shipping_cost" ng-model="shipping_cost" min="0" value="0" id="shipping_cost" class="form-control form-control-sm mb-1"></td>
                                        </tr>
                                        <tr>
                                            <td>Adjustment</td>
                                            <td><input type="number" style="text-align:left;" name="adjustment" ng-model="adjustment"  value="0" id="adjustment" class="form-control form-control-sm mb-1"></td>
                                        </tr>
                                        @if($settings->is_vat_registered) 
                                        <tr>
                                            <td>Tax {{$settings->tax_rate}} %</td>
                                            <td><b>@{{(sumTax(invoicetemp)) | number:0}}</b></td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td>Total Payable</td>
                                            <td><b>@{{(sum(invoicetemp)-discount)+(shipping_cost+adjustment)+(sumTax(invoicetemp)) | number:0}}</b></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button type="submit" name="myButton" class="btn btn-success btn-block">Create</button>
                                <a href="{{url('cancel-invoice')}}" type="button" class="btn btn-warning btn-block">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>      
    </div>
    

    <!-- Modal -->
<div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-bs-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">{{trans('navmenu.new_customer')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-bs-label="Close"></button>
                
            </div>
        <form class="form-validate" method="POST" action="{{url('new-customer')}}">
            <div class="modal-body">
            @csrf
            <div class="row col-xl-12">
                <div class=" col-md-6">
                      <label for="register-username" class="form-label">{{trans('navmenu.customer_name')}} <span style="color: red; font: bold;">*</span></label>
                      <input id="register-username" type="text" name="name" required placeholder="{{trans('navmenu.hnt_customer_name')}}" class="form-control">
                </div>
                
                <div class=" col-md-6">
                      <label for="register-username" class="form-label">{{trans('navmenu.phone_number')}}</label>
                      <input id="register-username" type="text" name="phone" placeholder="{{trans('navmenu.hnt_customer_mobile')}}" class="form-control"  data-inputmask='"mask": "9999999999"' data-mask>
                </div>
                
                <div class=" col-md-6">
                      <label for="register-email" class="form-label">{{trans('navmenu.email_address')}}</label>
                      <input id="register-email" type="text" name="email" placeholder="{{trans('navmenu.hnt_customer_email')}}" class="form-control">
                </div>
                <div class=" col-md-6">
                    <label for="address" class="form-label">{{trans('navmenu.postal_address')}}</label>
                    <input id="address" type="text" name="postal_address" placeholder="{{trans('navmenu.hnt_postal_address')}}" class="form-control">
                </div>

                <div class=" col-md-6">
                    <label for="address" class="form-label">{{trans('navmenu.physical_address')}}</label>
                    <input id="address" type="text" name="physical_address" placeholder="{{trans('navmenu.hnt_physical_address')}}" class="form-control">
                </div>

                <div class=" col-md-6">
                    <label for="address" class="form-label">{{trans('navmenu.street')}}</label>
                    <input id="address" type="text" name="street" placeholder="{{trans('navmenu.hnt_street')}}" class="form-control">
                </div>
                
                <div class=" col-md-6">
                      <label for="register-username" class="form-label">{{trans('navmenu.tin')}}</label>
                      <input id="register-username" type="text" name="tin" placeholder="{{trans('navmenu.hnt_customer_tin')}}" class="form-control"  data-inputmask='"mask": "999-999-999"' data-mask>
                </div>
                <div class=" col-md-6">
                      <label for="register-username" class="form-label">{{trans('navmenu.vrn')}}</label>
                      <input id="register-username" type="text" name="vrn" placeholder="{{trans('navmenu.hnt_customer_vrn')}}" class="form-control">
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
                
            </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn btn-success">{{trans('navmenu.btn_save')}}</button>
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
            </div>
        </form>
        </div>
    </div>
</div>

    <div class="modal" id="livestream_scanner">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                    <h4 class="modal-title">Barcode Scanner</h4>
                </div>
                <div class="modal-body" style="position: static">
                    <div id="interactive" class="viewport"></div>
                    <div class="error"></div>
                </div>
                <div class="modal-footer">
                    <label class="btn btn-default float-end">
                        <i class="bx bx-camera"></i> Use camera app
                        <input type="file" accept="image/*;capture=camera" capture="camera" class="hidden" />
                    </label>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


@endsection
<link rel="stylesheet" href="{{asset('css/DatePickerX.css')}}">
<script src="{{asset('js/DatePickerX.min.js')}}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="invoice_date"]'),
                $max = document.querySelector('[name="due_date"]');


            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                minDate    : new Date(),
                // maxDate    : new Date()
            });

        });
    </script>