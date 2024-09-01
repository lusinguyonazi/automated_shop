@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
    <script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/porder.js')}}"></script>
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
            window.location.href="{{url('cancel-porder')}}";
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
        var c = document.getElementById('paid-field');
        var ad = document.getElementById('amount_due');
        var acc = document.getElementById('account');

        var sbscr = "<?php echo $shop->subscription_type_id; ?>";
        if (sbscr == 2) {
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
</script>

<?php 
    // $role = Auth::user()->role();
    // $is_manager = true;
    // if ($role == 'manager') {
        $is_manager = true;
    // }
 ?>
<script type="text/javascript">

    $(document).ready(function(){

    var isManager = "<?php echo $is_manager; ?>";
        var mind = 3;
        if(isManager){
           mind = "<?php echo $mindays; ?>";
        }
        var d = new Date();
        d.setDate(d.getDate() - mind);

      $('#stock_date').bootstrapMaterialDatePicker({
        format: 'YYYY-MM-DD HH:mm',
        minDate : d,
        maxDate : new Date(),
      });

      $('#due_date').bootstrapMaterialDatePicker({
        format : 'YYYY-MM-HH:mm',
        minDate : new Date(),
      });

  }); 
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
            <h6 class="mb-0 text-uppercase">{{trans('navmenu.search_product')}}</h6>
            <hr>
            <div class="card radius-6">
                <div class="card-body">
                    <div class="col-sm-12 mb-3 text-center" >
                    {{-- <div class="col-sm-12 text-center mb-3" > --}}

                        <button type="button" class="btn btn-primary btn-sm"   data-bs-toggle="modal" data-bs-target="#productModal">
                            <i class="bx bx-plus mr-1"></i>
                            {{trans('navmenu.new_product')}}
                        </button>
                        {{-- <button type="button" class="btn btn-primary btn-sm"   data-toggle="modal" data-bs-target="#productModal">
                            <i class="bx bx-plus mr-1"></i>
                            {{trans('navmenu.new_product')}}
                        </button> --}}
                    </div>
                    <div class="col-sm-12">
                        <label class="form-label">Scan Barcode</label>
                        <input id="scanner_input_purchase" name="barcode" type="text" ng-model="barcode" class="form-control form-control-sm mb-3" placeholder="Scan barcode from an item ..." type="text" autofocus/>
                    </div>
                    <div class="col-sm-12">
                        <label class="form-label">{{trans('navmenu.search_product')}}</label> 
                        <input ng-model="searchKeyword" placeholder="{{trans('navmenu.search_product')}}" class="form-control form-control-sm mb-3">
                    </div>    
                    <table class="table table-hover" style="width: 100%; display: block; overflow: scroll; overflow: auto; flex: center;">
                        <tr ng-repeat="item in items  | filter: searchKeyword | limitTo:10">
                            <td>@{{item.name}}</td>
                            <td>
                                <a class="btn btn-success btn-sm" ng-click="addOrderTemp(item, newpordertemp)"><span class="lni lni-arrow-right" aria-hidden="true"></span></a>
                            </td>

                        </tr>
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <div class="col-xl-9 mx-auto">
            <h6 class="mb-0 text-uppercase">{{trans('navmenu.purchase_order_items')}}</h6>
            <hr>
            <div class=" card radius-6">
                <!-- /.box-header -->
                <div class="card-body">
                    <div class="d-lg-flex align-items-center mb-1 gap-1">
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#supplierModal"><i class="bx bx-user-plus"></i>{{trans('navmenu.new_supplier')}}</button>
                    </div>
                    <div class="p-4 border rounded">
                        <form class="row g-3 needs-validation" novalidate name="orderform" method="POST" action="{{route('purchase-orders.store')}}" onsubmit="return validateform(this)">
                            @csrf
                            <div class="col-sm-4">
                                <label for="supplier_id" class="form-label">{{trans('navmenu.supplier')}}</label>
                                <select name="supplier_id" id="supplier" required class="form-select mb-3 select2" onchange="changeSupplier(this)">
                                    <option value="0">{{trans('navmenu.unknown')}}</option>
                                    @foreach($suppliers as $key => $supplier)
                                    <option value="{{$key}}">{{$supplier}}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-sm-6">
                               <div class="form-group">
                                <label for="comments" class="form-label">{{trans('navmenu.comments')}}</label>
                                <textarea  class="form-control form-control-sm mb-3" name="comments" rows="1" id="comments" ></textarea>
                            </div>
                            </div>
                            <div class="col-md-12">
                                <table class="table table-responsive table-striped display nowrap" style="width: 100%; display: block; overflow: scroll; overflow: auto;">
                                     <tr>
                                        <th style="text-align: center;">#</th>
                                        <th style="text-align: center;">{{trans('navmenu.product_name')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.qty')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                        <th style="text-align: center;">&nbsp;</th>
                                    </tr>
                                    <tr ng-repeat="newpordertemp in pordertemp" id="temps">
                                        <td>@{{$index + 1}}</td>
                                        <td>@{{newpordertemp.product.name}}</td>
                                        <td><input type="number" name="qty" ng-blur="updateOrderTemp(newpordertemp)" ng-model="newpordertemp.qty" min="0" step="any" value="@{{newpordertemp.qty}}" style="text-align:center; height: 20px; width: 140px; border: 1px solid #e0e0e0;" autocomplete="off"></td>
                                        <td><input type="number" name="unit_cost" ng-blur="updateOrderTemp(newpordertemp)" ng-model="newpordertemp.unit_cost" min="0" step="any" value="@{{newpordertemp.unit_cost}}" style="text-align:center;height: 20px; width: 140px; border: 1px solid #e0e0e0;" autocomplete="off"></td>
                                        <td>@{{(newpordertemp.qty*newpordertemp.unit_cost)}}</td>
                                        <td><a href="#" ng-click="removeOrderTemp(newpordertemp.id)"><span class="bx bx-trash" aria-hidden="true" style="color: red"></span></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th><b>{{trans('navmenu.total')}}</b></th>
                                        <th></th>
                                        <th></th>
                                        <th><b>@{{sum(pordertemp) | number: 2}}</b></th>
                                        <th></th>
                                    </tr>
                                </table>
                            </div>

                            <div class="row">
                                <input type="hidden" id="no_items" name="no_items" value="@{{stocktemp.length}}" class="form-control form-control-sm mb-3">
                                <div class="col-xl-6">
                                    <button type="submit" name="myButton" class="btn btn-success btn-sm">{{trans('navmenu.btn_submit')}}</button>
                                    <button onclick="confirmCancel()" type="button" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</button>
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
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">�</span></button>
                <h4 class="modal-title" id="myModalLabel">New Supplier</h4>
            </div>
        <form class="form-validate" method="POST" action="{{route('suppliers.store')}}">
            <div class="modal-body">
            @csrf
            <div class="row">
                <input type="hidden" name="supplier_for" value="Stock">
                <div class="col-md-6">
                      <label for="register-username" class="label-control">Supplier Name</label>
                      <input id="register-username" type="text" name="name" required placeholder="Please enter supplier name" class="form-control form-control-sm">
                </div>
                
                <div class="col-md-6">
                      <label for="register-username" class="label-control">Phone number</label>
                      <input id="register-username" type="text" name="contact_no" placeholder="Please enter supplier mobile number" class="form-control form-control-sm">
                </div>
                
                <div class="col-md-6">
                      <label for="register-email" class="label-control">Email Address</label>
                      <input id="register-email" type="text" name="email" placeholder="Please enter supplier email address" class="form-control form-control-sm">
                </div>

                <div class="col-md-6">
                      <label for="address" class="label-control">Address</label>
                      <input id="address" type="text" name="address" placeholder="Please enter supplier address" class="form-control form-control-sm">
                </div>
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

{{-- <div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">�</span></button>
                <h4 class="modal-title" id="myModalLabel">{{trans('navmenu.new_product')}}</h4>
            </div>
            <form class="form-validate" method="POST" action="{{url('create-product')}}">
            <div class="modal-body">
                @csrf
                <div class="form-group col-md-6">
                    <label class="control-label">{{trans('navmenu.product_name')}} <span style="color: red; font-weight: bold;">*</span></label>
                    <input id="name" type="text" name="name" required placeholder="{{trans('navmenu.hnt_product_name')}}" class="form-control">
                </div>
                <div class="form-group col-md-6">
                    <label>{{trans('navmenu.basic_unit')}} <span style="color: red; font-weight: bold;">*</span></label>
                    <select class="form-control" name="basic_unit" required style="width: 100%;">
                        @foreach($units as $key => $unit)
                        <option value="{{$key}}">{{$unit}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label class="control-label">{{trans('navmenu.product_no')}}</label>
                    <input id="name" type="text" name="product_no" placeholder="{{trans('navmenu.hnt_product_no')}}" class="form-control">
                </div>
                <div class="form-group col-md-6">
                    <label>{{trans('navmenu.location')}}</label>
                    <input id="location" type="text" name="location" placeholder="{{trans('navmenu.hnt_location')}} (Optional)" class="form-control">
                </div>
                <div class="form-group col-md-6">
                    <label>{{trans('navmenu.selling_per_unit')}}</label>
                    <input id="unit_price" type="number" min="0" name="price_per_unit" placeholder="{{trans('navmenu.hnt_selling_price')}}" class="form-control">
                </div>
                @if($settings->is_vat_registered)
                <div class="form-group col-md-6">
                    <div class="checkbox icheck">
                        <label>
                            <input type="checkbox" name="with_vat" value="Yes">{{trans('navmenu.vat_desc_one')}} {{number_format($settings->tax_rate)}} {{trans('navmenu.vat_desc_two')}}
                        </label>
                    </div>
                </div>
                @endif
                <div class="form-group col-md-12">
                    <button type="submit" class="btn btn btn-success">Save</button>
                    <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>                    
            <div class="modal-footer">
                
            </div>
            </form>
        </div>
    </div>
</div> --}}

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
                    {{-- <button onclick="confirmCancel()" type="button" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</button> --}}
                    {{-- <button type="button" class="btn btn-warning btn-sm" data-dismiss="modal">Cancel</button> --}}
                </div>
            </form>
        </div>
    </div>
</div>

@endsection


