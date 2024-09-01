@extends('layouts.app')
<script type="text/javascript">
    function confirmDelete(id){
        document.getElementById('delete-form-'+id).submit();
    }
</script>
@section('content')

    <div class="row" ng-controller="SearchItemCtrl">
        <div class="col-md-12">
          
            <div class="card radius-10">
                <div class="card-header">
                    <h3 class="card-title">{{trans('navmenu.purchase_order_items')}}</h3>
                    
                    @if($porder->status == 'Pending')
                    <button type="button" class="btn btn-success pull-right" data-bs-toggle="modal" data-bs-target="#itemModal">
                        <i class="bx bx-shopping-cart"></i>
                        {{trans('navmenu.add_purchase_item')}}
                    </button>
                    @endif
                    <a href="{{url('purchase-orders')}}" class="btn btn-default pull-right"><i class="bx bx-arrow-left"></i>{{trans('navmenu.purchase_orders')}}</a>
                </div>
                <!-- /.box-header -->
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="supplier_id" class="form-label">{{trans('navmenu.supplier')}}</label>
                                @if(!is_null(App\Models\Supplier::find($porder->supplier_id)))
                                <input type="text" name="" class="form-control form-control-sm mb-3" value="{{App\Models\Supplier::find($porder->supplier_id)->name}}" readonly>
                                @else
                                <input type="text" name="" class="form-control form-control-sm mb-3" value="{{trans('navmenu.unknown')}}" readonly>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="total" class="form-label">{{trans('navmenu.total')}} </label>
                                <input type="text" name="" class="form-control form-control-sm mb-3" value="{{number_format($porder->amount, 2, '.', ',')}}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="comments" class="form-label">{{trans('navmenu.comments')}}</label>
                                <textarea  class="form-control form-control-sm mb-3" name="comments" id="comments" >{{$porder->comments}}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <table id="example1" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                            <tr>
                                <th>#</th>
                                <th>{{trans('navmenu.product_name')}}</th>
                                <th>{{trans('navmenu.qty')}}</th>
                                <th>{{trans('navmenu.unit_cost')}}</th>
                                <th>{{trans('navmenu.total')}}</th>
                                <th>{{trans('navmenu.actions')}}</th>
                            </tr>
                            @foreach($pitems as $key => $item)
                            <tr id="temps">
                                <td>{{$key + 1}}</td>
                                <td>{{$item->name}}</td>
                                <td>{{$item->qty}}</td>
                                <td>{{$item->unit_cost}}</td>
                                <td>{{($item->qty*$item->unit_cost)}}</td>
                            <td> 
                                <a href="{{ route('poitems.edit', encrypt($item->id))}}">
                                    <i class="bx bx-edit" style="color: blue;"></i>
                                </a> | 
                                <form id="delete-form-{{$item->id}}" method="POST" action="{{ route('poitems.destroy', encrypt($item->id))}}" style="display: inline;"> 
                                    @csrf
                                    @method("DELETE")
                                    <a href="javascript:;" class="text-danger" onclick=" return confirmDelete('<?php echo $item->id; ?>')"><span class="bx bx-trash" aria-hidden="true" style="color: red"></span></a>
                                </form>
                            </td>
                        </tr>                            
                        @endforeach
                    </table>
                </div>
            </div>
        </div>      
    </div>

<div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">�</span></button>
                <h4 class="modal-title" id="myModalLabel">{{trans('navmenu.add_purchase_item')}}</h4>
            </div>
            <form class="form-validate" method="POST" action="{{url('add-purchase-order-item')}}">
            <div class="modal-body">
                @csrf

                <input type="hidden" name="purchase_order_id" value="{{$porder->id}}">
                <div class="form-group col-md-6">
                    <label>{{trans('navmenu.product_name')}} <span style="color: red; font-weight: bold;">*</span></label>
                    <select class="form-control select2" id="my-select" name="product_id" required style="width: 100%;">
                        <option value="">Select Product</option>
                        @foreach($products as $key => $product)
                        <option value="{{$product->id}}">{{$product->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label class="control-label">{{trans('navmenu.quantity')}} <span style="color: red; font-weight: bold;">*</span></label>
                    <input id="name" type="number" name="qty" placeholder="{{trans('navmenu.hnt_enter_quantity')}}" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label>{{trans('navmenu.buying_per_unit')}}</label>
                    <input id="unit_price" type="number" min="0" name="unit_cost" placeholder="{{trans('navmenu.hnt_buying_price')}}" class="form-control" required>
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