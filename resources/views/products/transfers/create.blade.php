@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}" />
   <script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/transOrder.js')}}"></script>
<script type="text/javascript">
    
    function weg(elem) {
      var x = document.getElementById("date_field");
      if(elem.value !== "auto") {
        x.style.display = "block";
      } else {
        x.style.display = "none";
        $("#sale_date").val('');
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
                    <li class="breadcrumb-item"><a href="{{url('products')}}"><i class="bx bx-cart-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item"><a href="{{url('transfer-orders')}}">{{trans('navmenu.stock_transfer')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$title}}</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row" ng-controller="SearchItemCtrl">
        <div class="col-md-3 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{trans('navmenu.search_product')}}</h6>
            <hr>
            <div class="card radius-6">
                <div class="card-body">
                    <div class="col-sm-12">
                        <label class="form-label">{{trans('navmenu.search_product')}}</label> 
                        <input ng-model="searchKeyword" placeholder="{{trans('navmenu.search_product')}}" class="form-control form-control-sm mb-3">
                    </div>    
                    <table class="table table-hover" style="width: 100%; display: block; overflow: scroll; overflow: auto; flex: center;">
                        <tr ng-repeat="item in items  | filter: searchKeyword | limitTo:10">
                            <td>@{{item.name}}</td>
                            <td>
                                <span style="color: blue;" ng-if="item.in_stock > 0">(@{{item.in_stock}})</span>
                                <span style="color: red;" ng-if="item.in_stock == 0">(@{{item.in_stock}})</span>
                            </td>
                            <td>
                                <a class="btn btn-success btn-sm" ng-click="addOrderTemp(item, newstocktemp)"><span class="lni lni-arrow-right" aria-hidden="true"></span></a>
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <div class="col-xl-9 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{trans('navmenu.stock_transfer_order')}}</h6>
            <hr>
            <div class=" card radius-6">
                <!-- /.box-header -->
                <div class="card-body">
                    <form class="row g-3 needs-validation" novalidate name="orderform" method="POST" action="{{route('transfer-orders.store')}}" onsubmit="return validateform(this)">
                        @csrf
                        <div class="d-lg-flex align-items-center mb-1 gap-1">
                            <div>
                                <a href="{{url('/transformation-transfer/create')}}" class="btn btn-primary btn-sm pull-left">{{trans('navmenu.transformation_transfer')}}</a>
                            </div>
                            <div class="ms-auto">  
                                <button type="submit" class="btn btn-success btn-sm"><i class="bx bx-user-plus" ></i>{{trans('navmenu.create_order')}}
                                    </button>
                                <a href="{{url('cancel-order')}}" class="btn btn-warning btn-sm card-subtitle" id="btn-cancel"><i class="bx bx-cancle"></i>{{trans('navmenu.cancel_order')}}</a>
                            </div>
                        </div>
                        <div class="p-4 border rounded">
                           <div class="row "> 
                            <div class="col-sm-4">
                               <label class="form-label" for="order_no">{{trans('navmenu.sto_no')}}. <span style="color: red; font-weight: bold;">*</span></label>
                                <input type="text" name="order_no" class="form-control form-control-sm mb-1" placeholder="Enter your order Number" value="TO-{{sprintf('%05d', $order_no)}}" required readonly>
                            </div>
                            <div class="col-sm-4">
                                 <label for="date_set" class="form-label">{{trans('navmenu.date')}}</label>
                                <select name="date_set" id="date_set" onchange="weg(this)" class="form-control form-control-sm mb-1">
                                    <option value="auto">Auto</option>
                                    <option value="manaul">Manual</option>
                                </select>
                            </div>
                            
                            <div class="col-sm-3">
                               <div id="date_field" style="display: none;">
                                    <label for="order_date" class="form-label">{{trans('navmenu.pick_date')}} <span style="color: red; font-weight: bold;">*</span></label>
                                    <div class="input-group">
                                        <div id="calendar" class="input-group-text">
                                            <i class="bx bx-calendar"></i>
                                        </div>
                                        <input type="text" name="order_date" id="datepicker" class="form-control form-control-sm mb-1" placeholder="{{trans('navmenu.pick_date')}}"  aria-describedby="calendar">
                                    </div>
                                </div>
                            </div>
                        </div>

                            <div class="row pt-3">
                                <div class="col-md-6">
                                    <label for="shop_id" class="form-label">
                                        {{trans('navmenu.source_shop')}} <span style="color: red; font-weight: bold;">*</span>
                                    </label>
                                    <input type="text" name="shop_id" value="{{$shop->name}}" class="form-control form-control-sm mb-1" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="destin_id" class="form-label">
                                        {{trans('navmenu.destin_shop')}}<span style="color: red; font-weight: bold;">*</span>
                                    </label>
                                    <select class="form-select form-select-sm mb-1" name="destin_id" ng-model="destin_id" ng-change="getDestin(destin_id)"  required>
                                        <option value="">{{trans('navmenu.select_destin_shop')}}</option>
                                        @foreach($destinations as $key => $destin)
                                        <option value="{{$destin->id}}">{{$destin->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 pt-2">
                                        <label for="reason" class="form-label">{{trans('navmenu.reason')}} <span style="color: red; font-weight: bold;">*</span></label>
                                        <textarea name="reason" class="form-control form-control-sm mb-1" required placeholder="{{trans('navmenu.hnt_transfer_reason')}}"></textarea>
                                </div>     
                            </div>
                            <div class="clearfix pt-4" style="width: 100%; border-bottom: 2px solid #BBDEFB; margin-bottom: 10px; display: block; overflow: scroll; overflow: auto;"></div>

                            <div class="col-md-12">
                                <table border="0" cellspacing="0" cellpadding="0" class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th class="Item">{{trans('navmenu.item_name')}}</th>
                                            <th class="source">{{trans('navmenu.source_stock')}}</th>
                                            <th class="destin">{{trans('navmenu.destin_stock')}}</th>
                                            <th class="qty">{{trans('navmenu.transfer_qty')}}</th>
                                            <th class="qty">{{trans('navmenu.source_unit_cost')}}</th>
                                            <th class="qty">{{trans('navmenu.destin_unit_cost')}}</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="newordertemp in ordertemp" id="temps">
                                            <td>@{{$index + 1}}</td>
                                            <td class="item">@{{newordertemp.product.name}}</td>
                                            <td class="source">@{{newordertemp.source_stock | number:0}}</td>
                                            <td class="destin">@{{newordertemp.destin_stock | number:0}}</td>
                                            <td class="qty">
                                                <input type="number" style="text-align: center;" ng-blur="updateOrderTemp(newordertemp)" ng-model="newordertemp.quantity" min="0" step="any" value="@{{newordertemp.quantity}}" class="form-control form-control-sm mb-1">
                                            </td>
                                            <td class="qty">@{{newordertemp.source_unit_cost}}</td>
                                            <td class="qty">
                                                <input type="number" style="text-align: center;" ng-blur="updateOrderTemp(newordertemp)" ng-model="newordertemp.destin_unit_cost" min="0" step="any" value="@{{newordertemp.destin_unit_cost}}" class="form-control form-control-sm mb-1">
                                            </td>
                                            <td><a href="#" ng-click="removeOrderTemp(newordertemp.id)"><span class="bx bx-trash" aria-hidden="true" style="color: red"></span></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>  
                </div>
            </div>
        </div>      
    </div>
@endsection