@extends('layouts.app')
<script type="text/javascript">
    function weg(elem) {
        var x = document.getElementById("date_field");
        if(elem.value !== "auto") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
            $("#datepicker").val('');
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

    function showHideForm(elem) {
        var newform = document.getElementById('new-form');
        if (elem == 'show') {
            newform.style.display = 'block';
        }else{
            newform.style.display = 'none';
        }
    }

    function confirmDelete(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure_delete')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            document.getElementById('delete-form-'+id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function confirmDeleteDamage(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure_delete')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            document.getElementById('delete-form-damage-'+id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }


    function confirmDeleteUnit(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure_delete')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            document.getElementById('delete-unit-form-'+id).submit();
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }
</script>
@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('/home')}}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">

        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-3 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
            <hr>
            <table class="table table-striped" style="width: 100%;">
                <tbody>
                    <tr>
                        <td>{{trans('navmenu.retail_price')}}</td>
                        <td>{{number_format($product->pivot->price_per_unit, 2, '.', ',')}}</td>
                        <td>
                            <button  type="button" class="font-13  btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#sellingModal">{{trans('navmenu.new')}} </button>
                        </td>
                    </tr>
                    @if($settings->retail_with_wholesale)
                    <tr>
                        <td>{{trans('navmenu.wholesaleprice')}}</td>
                        <td>{{number_format($product->pivot->wholesale_price, 2, '.', ',')}}</td>
                        <td></td>
                    </tr>
                    @endif
                    <tr>
                        <td>{{trans('navmenu.buying_price')}}</td>
                        <td>{{number_format($product->pivot->buying_per_unit, 2, '.', ',')}}</td>
                        <td>
                            <button type="button" class="font-13  btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#buyingModal">{{trans('navmenu.new')}}</button>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            @if($settings->is_filling_station)
                                {{trans('navmenu.total_g_or_l')}}
                            @else
                                {{trans('navmenu.damaged')}}
                            @endif
                        </td>
                        <td>
                            @if($settings->is_filling_station) 
                                {{-($t_dam)}}
                            @else
                                {{$t_dam}}
                            @endif
                        </td>
                        <td>
                            <button type="button" class="mb-0 font-13  btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#damageModal">
                                @if($settings->is_filling_station)
                                    {{trans('navmenu.new_depth_measure')}}
                                @else
                                    {{trans('navmenu.new')}}
                                @endif
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>{{trans('navmenu.reorder_point')}}</td>
                        <td>{{number_format($product->pivot->reorder_point)}}</td>
                        <td>
                            <button type="button" class=" font-13 btn btn-dark btn-sm"  data-bs-toggle="modal" data-bs-target="#reorderModal">{{trans('navmenu.new')}}</button>
                        </td>
                    </tr>
                    <tr>
                        <td>{{trans('navmenu.location')}}</td>
                        <td>{{$product->pivot->location}}</td>
                        <td>
                            <button type="button" class=" font-13 btn  btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#locationModal" data-backdrop="static" data-keyboard="false">
                                {{trans('navmenu.new')}}
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>  
        </div>
        <div class="col-md-6">
            <h6 class="mb-0 text-uppercase text-center">Product Unit(s) <a href="#" class=" font-13 btn  btn-primary btn-sm float-end" onclick="showHideForm('show')">{{trans('navmenu.new')}}</a></h6>
            <hr>
            <form method="POST" action="{{ route('product-units.store') }}" id="new-form" style="display: none;">
                @csrf
                <input type="hidden" name="product_id" value="{{$product->id}}">
                <div class="row">
                    <div class="col-sm-4">
                        <label class="form-label">Unit</label>
                        <select class="form-select select2" name="unit_name" required>
                            <option value=""> ---Select--</option>
                            @foreach($units as $key => $unit)
                                <option value="{{$key}}">{{$unit}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-8">
                        <label class="form-label">Qty equivalent to Basic Unit</label>
                        <input class="form-control form-control-sm mb-3" type="number" name="qty_equal_to_basic" placeholder="Enter quantity" required>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label">Unit Price</label>
                        <input class="form-control form-control-sm mb-3" type="number" name="unit_price" placeholder="Enter Unit Price" required>
                    </div>
                    <div class="col-sm-6">
                        <a href="#" onclick="showHideForm('hide')" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</a>
                        <button type="submit" class="btn btn-success btn-sm">{{trans('navmenu.btn_save')}}</button>
                    </div>
                </div>
            </form>
            <table class="table table-striped" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Unit</th>
                        <th>Is Basic</th>
                        <th>QTY Equivalent</th>
                        <th>Unit Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($productunits as $index => $unit)
                    <tr>
                        <td>{{$units[$unit->unit_name]}} ({{$unit->unit_name}})</td>
                        @if($unit->is_basic)
                        <td>True</td>
                        <td>1</td>
                        @else
                        <td>False</td>
                        <td>1{{$unit->unit_name}} == {{$unit->qty_equal_to_basic}} {{$product->basic_unit}}</td>
                        @endif
                        <td>{{number_format($unit->unit_price, 2, '.', ',')}}</td>
                        <td>
                            <a href="{{ route('product-units.edit', encrypt($unit->id))}}"><i class="bx bx-edit"></i></a> |
                            <form id="delete-unit-form-{{$index}}" method="POST" action="{{ route('product-units.destroy', encrypt($unit->id))}}" style="display: inline;">
                                @csrf
                                @method("DELETE")
                                <a href="#" onclick="confirmDeleteUnit('<?php echo $index; ?>')"><i class="bx bx-trash" style="color: red;"></i></a>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if(!is_null($product->pivot->barcode))
            <div class="col">
                <div class="card radius-10 ">
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <h4 class="my-1">
                                    <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($product->pivot->barcode, $bsetting->code_type, $bsetting->width, $bsetting->height, [0, 0, 0], $bsetting->showcode)}}" alt="barcode" />
                                </h4>

                            </div>
                            <div class="col">
                                <a href="#" class="btn btn-flat bg-light-primary font-13" onclick="PrintImage('data:image/png;base64,{{DNS1D::getBarcodePNG($product->pivot->barcode, $bsetting->code_type, $bsetting->width, $bsetting->height, [0, 0, 0], $bsetting->showcode)}}'); return false;">
                                    <i class="bx bx-barcode"></i> PRINT</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if(!is_null($product->pivot->description))
            <div class="col">
                <div class="card radius-10 ">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0 text-secondary">{{trans('navmenu.description')}}</p>
                                <p class="mb-0 font-18 text-success" >@if($product->pivot->description != 'null'){{$product->pivot->description}}@endif </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        <div class="col-md-3">
            <h6 class="mb-0 text-uppercase text-center">Product Summary</h6>
            <hr>
            <table class="table table-striped" style="width: 100%;">
                <tbody>
                    <tr>
                        <th>{{trans('navmenu.purchased')}}</th>
                        <td><b>{{$t_in+0}}</b></td>
                    </tr>
                    <tr>
                        <th>{{trans('navmenu.sold')}}</th>
                        <td><b>{{$t_out+0}}</b></td>
                    </tr>
                    <tr>
                        <th>{{trans('navmenu.returned')}}</th>
                        <td><b>{{$returned+0}}</b></td>
                    </tr>
                    <tr>
                        <th>{{trans('navmenu.transfered')}}</th>
                        <td><b>{{$t_transfer+0}}</b></td>
                    </tr>
                    <tr>
                        <th>@if($settings->is_filling_station) {{trans('navmenu.total_g_or_l')}} @else {{trans('navmenu.damaged')}} @endif</th>
                        <td><b>@if($settings->is_filling_station) {{-($t_dam+0)}} @else {{$t_dam+0}} @endif</b></td>
                    </tr>
                    <tr>
                        <th>{{trans('navmenu.in_stock')}}</th>
                        <td><b>{{$product->pivot->in_stock+0}}</b></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-11 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{trans('navmenu.stock_history')}}</h6>
            <hr>
            <div class="card radius-10">
                <div class="card-body">
                    <ul class="nav nav-tabs " role="tablist">
                        <li class="nav-item" role="presentation">
                            <a href="#stock_purchases" class="nav-link active" role="tab" aria-selected="true" data-bs-toggle="tab">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon">
                                        <div class="tab-title font-15">{{trans('navmenu.stock_purchases')}}</div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a href="#sales_history" class="nav-link " role="tab" aria-selected="true" data-bs-toggle="tab">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon">
                                        <div class="tab-title font-15">{{trans('navmenu.sales_history')}}</div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        @if($transfers->count() > 0)
                        <li class="nav-item" role="presentation">
                            <a href="#transfered" class="nav-link " role="tab" aria-selected="true" data-bs-toggle="tab">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon">
                                        <div class="tab-title font-15">{{trans('navmenu.transfered')}}</div>
                                    </div>
                                </div>
                            </a>
                        </li>
                        @endif
                        <li class="nav-item" role="presentation">
                            <a href="#damaged_tab" class="nav-link " role="tab" aria-selected="true" data-bs-toggle="tab">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon">
                                        <div class="tab-title font-15">
                                            @if($settings->is_filling_station)
                                            {{trans('navmenu.depth_measures')}}@else
                                            {{trans('navmenu.damaged')}}
                                            @endif 
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="stock_purchases" role="tabpanel">
                            <table id="example1" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <th>#</th>
                                    <th>{{trans('navmenu.quantity')}}</th>
                                    <th>{{trans('navmenu.buying_price')}}</th>
                                    <th>{{trans('navmenu.source')}}</th>
                                    <th>{{trans('navmenu.purchase_date')}}</th>
                                    @if($settings->enable_exp_date)
                                    <th>{{trans('navmenu.expire_date')}}</th>
                                    @endif
                                    <th>{{trans('navmenu.actions')}}</th>
                                </thead>
                                <tbody>
                                    @foreach($stocks as $index => $stock)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>
                                            @if(is_numeric( $stock->quantity_in ) && floor( $stock->quantity_in ) != $stock->quantity_in) {{$stock->quantity_in}} @else {{number_format($stock->quantity_in)}} @endif
                                        </td>
                                        <td>{{number_format($stock->buying_per_unit, 2, '.', ',')}}</td>
                                        <td>{{$stock->source}}</td>
                                        <td>{{$stock->time_created}}</td>
                                        @if($settings->enable_exp_date)
                                        <td>{{$stock->expire_date}}</td>     
                                        @endif
                                        <td>
                                            @if(Auth::user()->hasRole('manager')|| Auth::user()->can('edit-stock'))
                                            <a href="{{route('stocks.edit' , encrypt($stock->id))}}"><i class="bx bx-edit" style="color: blue;"></i></a>
                                            <form id="delete-form-{{$index}}" method="POST" action="{{route('stocks.destroy' , encrypt($stock->id))}}" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <a href="javascript:;" class="text-danger" onclick=" return confirmDelete({{$index}})"><span class="bx bx-trash" aria-hidden="true" style="color: red"></span></a>
                                            </form>    
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="tab-pane fade " id="sales_history" role="tabpanel">
                            <table id="sales-history-table" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <th style="width: 10px">#</th>
                                    <th>{{trans('navmenu.date')}}</th>
                                    <!-- <th>{{trans('navmenu.product_name')}}</th> -->
                                    <th>{{trans('navmenu.qty')}}</th>
                                    <th>{{trans('navmenu.buying')}}</th>
                                    <th>{{trans('navmenu.total')}}</th>
                                    <th>{{trans('navmenu.selling')}}</th>
                                    <th>{{trans('navmenu.total')}} </th>
                                    <th>{{trans('navmenu.discount')}}</th>
                                    @if($settings->is_vat_registered)
                                    <th>{{trans('navmenu.vat')}} </th>
                                    @endif
                                    <td>{{trans('navmenu.actions')}}</td>
                                </thead>
                                <tbody>
                                    @foreach($sale_items as $index => $item)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{date('d M, Y', strtotime($item->created_at))}}</td>
                                         <!-- <td>{{$item->name}}</td> -->
                                        <td>{{$item->quantity_sold}}</td>
                                        <th>{{number_format($item->buying_per_unit, 2, '.', ',')}}</th>
                                        <td>{{number_format($item->buying_price, 2, '.', ',')}}</td>
                                        <td>{{number_format($item->price_per_unit, 2, '.', ',')}}</td>
                                        <td>{{number_format($item->price, 2, '.', ',')}}</td>
                                        <td>{{number_format($item->total_discount, 2, '.', ',')}}</td>
                                        @if($settings->is_vat_registered)
                                        <td>{{number_format($item->tax_amount,2, '.', ',')}}</td>
                                        @endif
                                        <td><a href="{{url('edit-sale-item/'.encrypt($item->id))}}"><i class="bx bx-edit" style="color: blue;"></i></a></td>
                                    </tr>  
                                    @endforeach
                                </tbody>
                            </table>
                            <!-- <a href="{{ url('set-actual-prices/'.encrypt($product->id))}}" onclick="confirm('Make sure you have set both actual Selling and Buying prices Before taking this Action. Take this action only if Items were sold with wrong prices. Are you sure to change the prices on sold items?')" class="btn btn-warning">Set Actual Prices</a> -->
                        </div>
                        <div class="tab-pane fade " id="transfered" role="tabpanel">
                            <table id="transfered-table" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <th>#</th>
                                    <th>{{trans('navmenu.order_no')}}</th>
                                    <th>{{trans('navmenu.transfer_date')}}</th>
                                    <th>{{trans('navmenu.quantity')}}</th>
                                    <th>{{trans('navmenu.destination')}}</th>
                                    <th>{{trans('navmenu.reason')}}</th>
                                    <th>{{trans('navmenu.transfer_by')}}</th>
                                    <th>{{trans('navmenu.record_at')}}</th>
                                </thead>
                                <tbody>
                                    @foreach($transfers as $index => $transfer)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{sprintf('%05d', $transfer->order_no)}}</td>
                                        <td>{{$transfer->order_date}}</td>
                                        <td>{{number_format($transfer->quantity)}}</td>
                                        <td>{{App\Shop::find($transfer->destination_id)->display_name}}</td>
                                        <td>{{$transfer->reason}}</td>
                                        <td>{{App\User::find($transfer->user_id)->first_name}}</td>
                                        <td>{{$transfer->created_at}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>                                    
                            </table>
                        </div>

                        <div class="tab-pane fade " id="damaged_tab" role="tabpanel">
                            <table id="damaged-table" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <th>#</th>
                                    @if($settings->is_filling_station)
                                    <td>{{trans('navmenu.depth_measure')}}</td>
                                    <td>{{trans('navmenu.in_stock')}}</td>
                                    @endif
                                    <th>{{trans('navmenu.quantity')}}</th>
                                    <th>{{trans('navmenu.damage_cause')}}</th>
                                    <th>{{trans('navmenu.damage_date')}}</th>
                                    <th>{{trans('navmenu.actions')}}</th>
                                </thead>
                                <tbody>
                                    @foreach($damages as $index => $damage)
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        @if($settings->is_filling_station)
                                        <td>{{$damage->deph_measure}}</td>
                                        <td>{{$damage->in_stock}}</td>
                                        <td>{{-($damage->quantity)}} 
                                        </td>
                                        @else
                                        <td>{{$damage->quantity}} 
                                        </td>
                                        @endif
                                        <td>{{$damage->reason}}</td>
                                        <td>{{$damage->created_at}}</td>
                                        <td>
                                            <a href="{{route('damages.edit' , encrypt($damage->id))}}"><i class="bx bx-edit" style="color: blue;"></i></a>
                                            <form id="delete-form-damage-{{$index}}" method="POST" action="{{route('damages.destroy' , encrypt($damage->id))}}" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <a href="javascript:;" class="text-danger" onclick=" return confirmDeleteDamage({{$index}})"><span class="bx bx-trash" aria-hidden="true" style="color: red"></span></a>
                                            </form>   
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
             </div>
            
        </div>
        <div class="col-md-3">
            
        </div>
    </div>
      
<!-- Modal -->
<div class="modal fade" id="reorderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">{{trans('navmenu.new_reorder_point')}} </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="form-horizontal" method="POST" action="{{url('new-reorder-point')}}">
                <div class="modal-body row">
                    @csrf
                    <input type="hidden" name="product_id" value="{{$product->id}}">
                    <div class="col-md-12">
                        <label class="form-label">{{trans('navmenu.reorder_point')}}</label>
                        <input id="register-username" type="number" min="0" name="reorder_point" required placeholder="{{trans('navmenu.hnt_reorder_point')}}" class="form-control form-control-sm mb-1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm">{{trans('navmenu.btn_save')}}</button>
                    <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">{{trans('navmenu.new_location')}} </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="form-horizontal" method="POST" action="{{url('new-location')}}">
                <div class="modal-body row">
                    @csrf
                    <input type="hidden" name="product_id" value="{{$product->id}}">
                    <div class="col-md-12">
                        <label class="form-label">{{trans('navmenu.location')}}</label>
                        <input id="register-username" type="text" min="0" name="location" required placeholder="{{trans('navmenu.hnt_location')}}" class="form-control form-control-sm mb-1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm">{{trans('navmenu.btn_save')}}</button>
                    <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="buyingModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">{{trans('navmenu.new_buying_price')}} </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        <form class="form-horizontal" method="POST" action="{{url('new-buy-price')}}">
            <div class="modal-body">
                @csrf
                <input type="hidden" name="product_id" value="{{$product->id}}">
                <div class="col-md-12">
                    <label class="form-label">{{trans('navmenu.buying_per_unit')}}</label>
                    <input id="register-username" type="number" min="0" step="any" name="buying_per_unit" required placeholder="{{trans('navmenu.hnt_buying_price')}}" class="form-control form-control-sm mb-1">
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

<!-- Modal -->
<div class="modal fade" id="sellingModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">{{trans('navmenu.new_selling_price')}} </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        <form class="form" method="POST" action="{{url('new-sell-price')}}">
            <div class="modal-body row g-3">
                @csrf
                <input type="hidden" name="product_id" value="{{$product->id}}">
                <div class="col-md-6">
                    <label for="register-username" class="form-label">{{trans('navmenu.selling_per_unit')}}</label>
                    <input id="register-username" type="number" min="0" step="any" name="new_unit_price" required placeholder="{{trans('navmenu.hnt_selling_price')}}" value="{{$product->pivot->price_per_unit}}" class="form-control form-control-sm mb-1">  
                </div>
                @if($settings->retail_with_wholesale)
                <div class="col-md-6">
                    <label for="register-username" class="form-label">{{trans('navmenu.wholesale_price')}}</label>
                    <input id="register-username" type="number" min="0" step="any" name="wholesale_price" placeholder="{{trans('navmenu.hnt_selling_price')}}" value="{{$product->pivot->wholesale_price}}" class="form-control form-control-sm mb-1">  
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success btn-sm">{{trans('navmenu.btn_save')}}</button>
                <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
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
                <h4 class="modal-title" id="myModalLabel">
                @if($settings->is_filling_station)
                {{trans('navmenu.new_depth_measure')}}
                @else{{trans('navmenu.new_damage')}}@endif </h4>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form class="form" method="POST" action="{{route('damages.store')}}">
                <div class="modal-body row">
                    @csrf
                    <input type="hidden" name="product_id" value="{{$product->id}}">
                    @if($settings->is_filling_station)
                    <div class="col-md-6">
                        <label class="form-label">{{trans('navmenu.depth_measure')}}<span style="color: red;"> *</span></label>
                        <input id="deph_measure" type="number" step="any" name="deph_measure" placeholder="{{trans('navmenu.hnt_enter_quantity')}}" class="form-control form-control-sm mb-1">
                    </div>
                    @else
                    <div class="col-md-6">
                        <label class="form-label">{{trans('navmenu.quantity')}}<span style="color: red;"> *</span></label>
                        <input id="damaged" type="number" min="0" step="any" name="quantity" placeholder="{{trans('navmenu.hnt_enter_quantity')}}" class="form-control form-control-sm mb-1">
                    </div>
                    @endif
                    <div class="col-md-6">
                        <label class="form-label">{{trans('navmenu.date')}}</label>
                        <select onchange="wegDam(this)" class="form-select form-select-sm mb-3">
                            <option value="auto">Auto</option>
                            <option value="manual">Manual</option>
                        </select>
                    </div>
                    <div class="col-md-6" id="dam_date_field" style="display: none;">
                        <label class="form-label">{{trans('navmenu.pick_date')}}</label>
                        <div class="inner-addon left-addon">
                            <i class="myaddon bx bx-calendar"></i>
                            <input type="text" name="dam_date" id="dam_date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-3">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">{{trans('navmenu.damage_cause')}}<span style="color: red;"> *</span></label>
                        <textarea name="reason" placeholder="{{trans('navmenu.hnt_damage_cause')}}" class="form-control form-control-sm mb-3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm">{{trans('navmenu.btn_save')}}</button>
                    <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 
<link rel="stylesheet" href="../css/DatePickerX.css">

<script src="../js/DatePickerX.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="mnf_date"]'),
                $max = document.querySelector('[name="exp_date"]'),
                $dam = document.querySelector('[name="dam_date"]');

            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    :  new Date(),
            });

            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                minDate    : $min,
                // maxDate    : new Date()
            });

            $dam.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    :  new Date(),
            });

        });
    </script>

    <script>

        function ImagetoPrint(source)
        {
            return "<html><head><scri"+"pt>function step1(){\n" +
                    "setTimeout('step2()', 10);}\n" +
                    "function step2(){window.print();window.close()}\n" +
                    "</scri" + "pt></head><body onload='step1()'>\n" +
                    "<img src='" + source + "' /></body></html>";
        }

        function PrintImage(source)
        {
            var Pagelink = "about:blank";
            var pwa = window.open(Pagelink, "_new");
            pwa.document.open();
            pwa.document.write(ImagetoPrint(source));
            pwa.document.close();
        }

    </script>
