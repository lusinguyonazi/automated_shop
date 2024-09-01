@extends('layouts.app')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/expense.js')}}"></script>
    <script>

        function confirmDelete(id) {
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
                    window.location.href="{{url('delete-expense/')}}/"+id;
                    Swal.fire(
                        "{{trans('navmenu.deleted')}}",
                        "{{trans('navmenu.cancelled')}}",
                        'success'
                    )
                }
            })
        }
        
        function yesnoCheck(elem) {
            var x = document.getElementById("ifYes");
            if(elem.value !== "no") {
                x.style.display = "block";
            } else {
                x.style.display = "none";
                $("#wtax_rate").val('');
            }

        }

        function validateform(form) {
            var items = document.expenseform.no_items.value;
            if (items == 0) {
                // alert('Please select at least one item to continue.');
                Swal.fire(
                  'Nothing To Submit!',
                  'Please select at least one item to continue.',
                  'info'
                )
                return false;
            }

            var exptype = document.getElementById('exp_type');
            if (exptype.value == 'credit') {
                var supp = document.getElementById('supplier');
                if (supp.value == 0) {
                    // alert('Please select at least one item to continue.');
                    Swal.fire(
                      'No Supplier selected!',
                      'Please select a supplier for credit expense.',
                      'info'
                    )
                    return false;
                }
            }
            form.myButton.disabled = true;
            form.myButton.value = "Please wait...";
            return true;
            
        }

        function validateformModal() {

            var exptype = document.getElementById('exp_type-m');
            if (exptype.value == 'credit') {
                var supp = document.getElementById('supplier-m');
                if (supp.value == 0) {
                    $("#newTypeModal").modal('hide');
                    // alert('Please select at least one item to continue.');
                    Swal.fire(
                      'No Supplier selected!',
                      'Please select a supplier for credit expense.',
                      'info'
                    )
                    return false;
                }
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
                window.location.href="{{url('cancel-expense')}}";
                Swal.fire(
                  "{{trans('navmenu.deleted')}}",
                  "{{trans('navmenu.cancelled')}}",
                  'success'
                )
              }
            })
        }



        function weg(elem) {
          var x = document.getElementById("expense_date_field");
          if(elem.value !== "auto") {
            x.style.display = "block";
          } else {
            x.style.display = "none";
            $("#stock_date").val('');
          }
        }

        function wegExpType(elem) {
            var acc = document.getElementById('account');

            var sbscr = "<?php echo $shop->subscription_type_id; ?>";
            if (sbscr == 2) {
                var or = document.getElementById('order_no');
                var inv = document.getElementById('invoice_no');
                if (elem.value === "credit") {
                    var supp = document.getElementById('supplier');
                    acc.style.display = "none";
                    if (supp.value != 0) {
                        or.style.display = "block";                   
                        inv.style.display = " block";
                    }
                }else{
                    acc.style.display = "block";
                    or.style.display = "none";
                    inv.style.display = "none";
                }
            }else{
                if (elem.value === "credit") {
                    acc.style.display = "none";
                }else{
                    acc.style.display = "block";
                }
            }
        }

        function wegExpTypeModal(elem) {
            var acc = document.getElementById('account-m');

            var sbscr = "<?php echo $shop->subscription_type_id; ?>";
            if (sbscr == 2) {
                var or = document.getElementById('order_no-m');
                var inv = document.getElementById('invoice_no-m');
                acc.style.display = "none";
                if (elem.value === "credit") {
                    or.style.display = "block";                   
                    inv.style.display = " block";
                }else{
                    acc.style.display = "block";
                    or.style.display = "none";
                    inv.style.display = "none";
                }
            }else{
                if (elem.value === "credit") {
                    acc.style.display = "none";
                }else{
                    acc.style.display = "block";
                }
            }
        }

        function showModal(id) {
            $('#id_hide').val(id);
            $('#payModal').modal('show');
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
        <div class="col-md-4 mx-auto">
            <h6 class="mb-0 text-uppercase">{{trans('navmenu.search_expense_type')}}</h6>
            <hr>
            <div class="card radius-6">
                <div class="card-body">
                    <div class="d-lg-flex align-items-center mb-1 gap-1">
                        <div class="ms-auto">
                            <button type="button" class="btn btn-primary px-1" data-bs-toggle="modal" data-bs-target="#newTypeModal">
                                <i class="bx bx-plus mr-1"></i>
                                {{trans('navmenu.new_type')}}
                            </button>
                        </div>
                    </div>
                    <div class="p-2 border rounded">
                        <div class="form-group">
                            <label class="form-label">{{trans('navmenu.search_tap')}}</label> 
                            <input ng-model="searchKeyword" placeholder="{{trans('navmenu.search_expense_type')}}" class="form-control form-control-sm mb-1">
                        </div>   
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center" ng-repeat="item in items | filter: searchKeyword | limitTo:10" ng-click="addExpenseTemp(item, newexpensetemp)">@{{item.name}} <br><small style="color: gray;">@{{item.category}}</small>  <span class="badge bg-success rounded-pill"><span class="bx bx-redo" aria-hidden="true"></span></span></li>
                        </ul>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <div class="col-xl-8 mx-auto">
            <h6 class="mb-0 text-uppercase">{{trans('navmenu.expenses')}}</h6>
            <hr>
            <div class=" card radius-6">
                <!-- /.box-header -->
                <div class="card-body">
                    <div class="d-lg-flex align-items-center mb-1 gap-1">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#supplierModal"><i class="fa fa-user-plus"></i>{{trans('navmenu.new_supplier')}}
                            </button>
                    </div>
                    <div class="p-4 border rounded">
                        <form class="row g-3 needs-validation" novalidate name="expenseform" method="POST" action="{{ route('expenses.store') }}" onsubmit="return validateform(this)">
                            @csrf
                            <div class="col-sm-3">
                                <label for="supplier_id" class="form-label">{{trans('navmenu.supplier')}}</label>
                                <select name="supplier_id" id="supplier" required class="form-select mb-3 select2" onchange="changeSupplier(this)">
                                    <option value="0">{{trans('navmenu.unknown')}}</option>
                                    @foreach($suppliers as $key => $supplier)
                                    <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-sm-3">
                                <label for="date_set" class="form-label">{{trans('navmenu.expense_date')}}</label>
                                <select name="date_set" id="date_set" onchange="weg(this)" class="form-select form-select-sm mb-3">
                                    <option value="auto">Auto</option>
                                    <option value="manaul">Manual</option>
                                </select>
                            </div>
                            <div class="col-sm-3" id="expense_date_field" style="display: none;">
                                <label class="form-label">{{trans('navmenu.expense_date')}}</label>
                                <div class="inner-addon left-addon"> 
                                    <i class="myaddon bx bx-calendar"></i>
                                    <input type="text" name="expense_date" id="expense_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-3">
                                </div>
                            </div> 
                            
                            <div class="col-sm-3">
                                <div id="purchase_type_field">
                                    <label class="form-label">{{trans('navmenu.exp_type')}}</label>
                                    <select name="exp_type" id="exp_type" onchange="wegExpType(this)" class="form-select form-select-sm mb3" required>
                                        <option value="">{{trans('navmenu.select_purchase_type')}}</option>
                                        <option value="cash">{{trans('navmenu.cash_purchases')}}</option>
                                        <option value="credit">{{trans('navmenu.credit_purchases')}}</option>
                                    </select>
                                </div>
                            </div>

                            @if($settings->is_categorized)
                                <div class="col-sm-3">
                                    <label for="supplier_id" class="form-label">{{trans('navmenu.category')}}</label>
                                    <select name="category_id" id="category" class="form-control">
                                        <option value="">{{trans('navmenu.all_categories')}}</option>
                                        @if(!is_null($categories))
                                        @foreach($categories as $key => $category)
                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            @endif

                            <div class="col-md-3">
                                <div id="account" style="display: none;">
                                    <label for="account" class="form-label">{{trans('navmenu.paid_from')}} <span  style="color: red; font-weight: bold;">*</span></label>
                                    <select class="form-select form-select-sm mb3" name="account" required>
                                        <option value="Cash">{{trans('navmenu.cash')}}</option>
                                        <option value="Bank">{{trans('navmenu.bank')}}</option>
                                        <option value="Mobile Money">{{trans('navmenu.mobilemoney')}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div id="order_no" style="display: none;">
                                    <label for="total" class="form-label">{{trans('navmenu.purchase_order_no')}}</label>
                                    <input type="text" class="form-control form-control-sm mb-3" id="ord_no" placeholder="{{trans('navmenu.hnt_order_no')}}" name="order_no" />
                                </div> 
                            </div>

                            <div class="col-md-3">
                                <div  id="invoice_no" style="display: none;">
                                    <label for="total" class="form-label">{{trans('navmenu.invoice_no')}}</label>
                                    <input type="text"  class="form-control form-control-sm mb-3" id="inv_no" placeholder="{{trans('navmenu.hnt_invoice_no')}}" name="invoice_no" />
                                </div> 
                            </div>
                            @if($settings->is_service_per_device)
                            <div class="col-md-6">
                                <label class="control-label">{{trans('navmenu.device_number')}}</label>
                                <select name="device_id" class="form-control">
                                    <option value="">{{trans('navmenu.select_device')}}</option>
                                    @if(!is_null($devices))
                                    @foreach($devices as $device)
                                    <option value="{{$device->id}}">{{$device->device_number}}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            @endif

                            <div class="col-md-12">
                                <span class="text-center" style="color: red;">{{trans('navmenu.exp_note')}}</span>
                                <table class="table table-responsive table-striped display nowrap" style="width: 100%; display: block; overflow: scroll; overflow: auto;">
                                    <tr>
                                        <th style="text-align: center;">#</th>
                                        <th style="text-align: center;">{{trans('navmenu.expense_type')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.amount')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.no_days')}}</th>
                                        <td style="text-align: center;">{{trans('navmenu.description')}}</td>
                                        @if($settings->is_vat_registered)
                                        <th style="text-align: center;">{{trans('navmenu.has_vat')}}</th>
                                        <th>{{trans('navmenu.vat')}}</th>
                                        @endif
                                        @if($settings->estimate_withholding_tax)
                                        <th style="text-align: center;">{{trans('navmenu.wht_rate')}}</th>
                                        @endif
                                        <th style="text-align: center;">&nbsp;</th>
                                    </tr>
                                    <tr ng-repeat="newexpensetemp in expensetemp" id="temps">
                                        <td>@{{$index + 1}}</td>
                                        <td>@{{newexpensetemp.expense_type}}</td>
                                        <td><input type="number" name="amount" ng-blur="updateExpenseTemp(newexpensetemp)" string-to-number ng-model="newexpensetemp.amount" min="0" step="any" value="@{{newexpensetemp.amount}}" style="text-align:center;" autocomplete="off" class="form-control form-control-sm mb-1"></td>
                                        <td><input type="number" name="no_days" ng-blur="updateExpenseTemp(newexpensetemp)" ng-model="newexpensetemp.no_days" min="1" step="1" value="@{{newexpensetemp.no_days}}" style="text-align:center;" autocomplete="off" class="form-control form-control-sm mb-1"></td>
                                        <td><input type="text" name="description" ng-model="newexpensetemp.description" ng-blur="updateExpenseTemp(newexpensetemp)" class="form-control form-control-sm mb-1" value="@{{newexpensetemp.description}}"></td>
                                        @if($settings->is_vat_registered)
                                        <td>
                                            <select ng-model="newexpensetemp.has_vat" name="has_vat" ng-change="updateExpenseTemp(newexpensetemp)" class="form-control form-control-sm mb-1" style="border: 1px solid #e0e0e0;">
                                                <option value="no">{{trans('navmenu.no')}}</option>
                                                <option value="yes">{{trans('navmenu.yes')}}</option>
                                            </select>
                                        </td>
                                        <td>@{{newexpensetemp.vat_amount | number:2}}</td>
                                        @endif
                                        @if($settings->estimate_withholding_tax)
                                        <td><input type="number" name="wht_rate" ng-blur="updateExpenseTemp(newexpensetemp)" ng-model="newexpensetemp.wht_rate" min="0" step="any" value="@{{newexpensetemp.wht_rate}}" style="text-align:center;" autocomplete="off" class="form-control form-control-sm mb-1"></td>
                                        @endif
                                        <td><a href="#" ng-click="removeExpenseTemp(newexpensetemp.id)"><span class="bx bx-trash" aria-hidden="true" style="color: red"></span></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th></th><th>{{trans('navmenu.total')}}</th>
                                        <th style="text-align: center;"><b>@{{sum(expensetemp) | number:2}}</b></th><th></th><th></th>
                                        @if($settings->is_vat_registered)
                                        <th></th><th></th>
                                        @endif
                                        @if($settings->estimate_withholding_tax)
                                        <th></th>
                                        @endif
                                        <th></th>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-12 text-center">
                                <button type="submit" name="myButton" class="btn btn-success btn-block">{{trans('navmenu.btn_submit')}}</button>
                                <button onclick="confirmCancel()" type="button" class="btn btn-warning btn-block">{{trans('navmenu.btn_cancel')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>      
    </div>

      <!-- Modal -->
    <div class="modal fade" id="newTypeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="myModalLabel">{{trans('navmenu.new_type')}}</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> 
                </div>
                    <form method="POST" action="{{url('store-expenses')}}">
                        <div class="modal-body">
                        @csrf
                            <div class="row align-items-center">
                                <div class="col-md-6 pt-2">
                                    <label for="register-username" class="form-label">{{trans('navmenu.expense_type')}} <span style="color: red;">*</span></label>
                                      <input id="register-username" type="text" name="expense_type" required placeholder="{{trans('navmenu.hnt_expense_type')}}" class="form-control form-control-sm mb-1">
                                      
                                </div>
                                <div class="col-md-6 pt-2">
                                    <label class="form-label">{{trans('navmenu.amount')}}  <span style="color: red;">*</span></label>
                                    <input type="number" name="amount" placeholder="{{trans('navmenu.hnt_amount')}}" class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-md-6 pt-2">
                                    <label class="form-label">Expense Category</label>
                                    <select name="expense_category_id" class="form-select form-select-sm mb-1">
                                        <option value="">None</option>
                                        @foreach($expcategories as $expcat)
                                        <option value="{{$expcat->id}}">{{$expcat->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class=" col-md-6 pt-2">
                                    <label for="no_days" class="form-label">{{trans('navmenu.no_days')}}</label>
                                    <input type="number" name="no_days" class="form-control form-control-sm mb-1" value="1" min="1" placeholder="Enter no of days">
                                </div>
                                @if($settings->is_vat_registered)
                                <div class="col-md-6 pt-2">
                                    <label class="form-label">{{trans('navmenu.has_vat')}}</label>
                                    <select name="has_vat" class="form-control form-control-sm mb-1">
                                        <option value="no">NO</option>
                                        <option value="yes">YES</option>
                                    </select>
                                </div>
                                @endif
                                
                                @if($settings->estimate_withholding_tax)
                                <div class="col-md-6 pt-2">
                                    <label class="form-label">Is this Expense contains Withholding Tax</label>
                                    <select onchange="yesnoCheck(this)" class="form-control form-control-sm mb-1">
                                        <option value="no">NO</option>
                                        <option value="yes">YES</option>
                                    </select>
                                </div>
                                <div class="col-md-6 pt-2" id="ifYes" style="display: none;">
                                    <label>Withholding Tax Rate(%): </label>
                                    <input type='number' min="0" id='wtax_rate' name='wht_rate' class="form-control form-control-sm mb-1" placeholder="Please Enter the Rate(%) of Withholding Tax">
                                </div>
                                @endif
                               
                                <div class="col-md-6 pt-2">
                                    <label for="supplier_id" class="form-label">{{trans('navmenu.supplier')}}</label>
                                    <select name="supplier_id" id="supplier-m" required class="form-select form-select-sm mb-3" onchange="changeSupplier(this)">
                                        <option value="0">{{trans('navmenu.unknown')}}</option>
                                        @foreach($suppliers as $key => $supplier)
                                        <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 pt-2">
                                    <label class="form-label">{{trans('navmenu.exp_type')}} <span style="color: red;">*</span></label>
                                    <select name="exp_type" id="exp_type-m" onchange="wegExpTypeModal(this)" class="form-select form-select-sm mb-3" required>
                                        <option value="">{{trans('navmenu.select_exp_type')}}</option>
                                        <option value="cash">{{trans('navmenu.cash_exp')}}</option>
                                        <option value="credit">{{trans('navmenu.credit_exp')}}</option>
                                    </select>
                                </div>
                                @if($settings->is_categorized)
                                <div class=" col-md-6 pt-2">
                                    <label class="form-label">{{trans('navmenu.category')}}</label>
                                    <select name="category_id" id="category" class="form-select form-select-sm mb-3">
                                        <option value="">{{trans('navmenu.all_categories')}}</option>
                                        @if(!is_null($categories))
                                        @foreach($categories as $key => $category)
                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                                @endif
                                <div class=" col-md-6 pt-2" id="account-m" style="display: none;">
                                    <label for="account" class="form-label">{{trans('navmenu.paid_from')}} <span  style="color: red; font-weight: bold;">*</span></label>
                                    <select class="form-control form-control-sm mb-1" name="account" required>
                                        <option value="Cash">{{trans('navmenu.cash')}}</option>
                                        <option value="Bank">{{trans('navmenu.bank')}}</option>
                                        <option value="Mobile Money">{{trans('navmenu.mobilemoney')}}</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 pt-2" id="order_no-m" style="display: none;">
                                    <label for="total" class="form-label">{{trans('navmenu.purchase_order_no')}}</label>
                                    <input type="text" class="form-control form-control-sm mb-1" id="ord_no" placeholder="{{trans('navmenu.hnt_order_no')}}" name="order_no" />
                                </div> 
                                <div class="col-md-6" id="invoice_no-m" style="display: none;">
                                    <label for="total" class="form-label">{{trans('navmenu.invoice_no')}}</label>
                                    <input type="text"  class="form-control form-control-sm mb-1" id="inv_no" placeholder="{{trans('navmenu.hnt_invoice_no')}}" name="invoice_no" />
                                </div>


                                @if($settings->is_service_per_device)
                                <div class="col-md-6 pt-2">
                                    <label class="form-label">{{trans('navmenu.device_number')}}</label>
                                    <select name="device_id" class="form-control form-control-sm mb-1">
                                        <option value="">{{trans('navmenu.select_device')}}</option>
                                        @if(!is_null($devices))
                                        @foreach($devices as $device)
                                        <option value="{{$device->id}}">{{$device->device_number}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                                @endif

                            </div>

                            <div class="col-md-12">
                                <label class="form-label">{{trans('navmenu.description')}}</label>
                                <textarea name="description" class="form-control form-control-sm mb-1" placeholder="{{trans('navmenu.hnt_description')}}"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn btn-success" onclick="return validateformModal()" name="myButton">{{trans('navmenu.btn_save')}}</button>
                                <button type="button" class="btn btn-warning" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                            </div>
                        </div>
                    </form>
                </div>
        </div>
    </div>

       <!-- Modal -->
    <div class="modal fade" id="supplierModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="myModalLabel">New Supplier</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    
                </div>
                <form class="form-validate" method="POST" action="{{route('suppliers.store')}}">
                    <div class="modal-body row">
                    @csrf
                        <input type="hidden" name="supplier_for" value="Expense">
                        <div class=" col-md-6">
                            <label for="register-username" class="form-label">Supplier Name</label>
                            <input id="register-username" type="text" name="name" required placeholder="Please enter supplier name" class="form-control form-control-sm mb-3">
                        </div>
                        <div class=" col-md-6">
                            <label for="register-username" class="form-label">Phone number</label>
                            <input id="register-username" type="text" name="contact_no" placeholder="Please enter supplier mobile number" class="form-control form-control-sm mb-3">
                        </div>
                            
                        <div class=" col-md-6">
                            <label for="register-email" class="form-label">Email Address</label>
                            <input id="register-email" type="text" name="email" placeholder="Please enter supplier email address" class="form-control form-control-sm mb-3">
                        </div>
                        <div class=" col-md-6 pt-2">
                            <label for="address" class="form-label">Address</label>
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
            var $min = document.querySelector('[name="expense_date"]');

            var isManager = "<?php echo $is_manager; ?>";
            var mind  = "<?php echo $mindays; ?>";
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