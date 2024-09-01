@extends('layouts.app')

@section('content')

      <!-- SELECT2 EXAMPLE -->
      <div class="card radius-10">
        <div class="card-header">
          <h6 class="card-title">
            <ul class="row">
              <li class="col">{{trans('navmenu.purchase_date')}} : <b>{{date("d, M Y", strtotime($purchase->time_created))}}</b></li>
              <li class="col">{{trans('navmenu.total_amount')}} : <b>{{number_format($purchase->total_amount)}}</b></li>
              <li class="col">{{trans('navmenu.amount_paid')}} : <b>{{number_format($purchase->amount_paid)}}</b></li>
              <li class="col">{{trans('navmenu.unpaid')}} : <b>{{number_format($purchase->total_amount-$purchase->amount_paid)}}</b></li>
            </ul>
          </h6>
        </div>
        <!-- /.box-header -->
        <div class="card-body">
          <div class="row">

        	<form class="form-validate" method="POST" action="{{route('purchases.update' , encrypt($purchase->id))}}">
        		@csrf
            @method('PUT')
            <div class="row">
              <div class="col-md-3">
                <div class="form-group pt-2">
                  <label class="form-label">{{trans('navmenu.supplier')}}</label>
                  <select name="supplier_id" class="form-control">
                    @if(!is_null(App\Models\Supplier::find($purchase->supplier_id)))
                    <option value="{{$purchase->supplier_id}}">{{App\Models\Supplier::find($purchase->supplier_id)->name}}</option>
                    @else
                    <option>{{trans('navmenu.unknown')}}</option>
                    @endif
                    @foreach($suppliers as $supplier)
                    <option value="{{$supplier->id}}">{{$supplier->name}}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              <div class="col-md-3">
                <div class="form-group pt-2" id="order_no">
                    <label for="total" class="form-label">{{trans('navmenu.purchase_order_no')}}</label>
                    <input type="text" class="form-control" id="ord_no" placeholder="{{trans('navmenu.hnt_order_no')}}" name="order_no" value="{{$purchase->order_no}}" />
                </div> 
              </div>
              <div class="col-md-3">
                <div class="form-group pt-2" id="delivery_note_no">
                  <label for="total" class="form-label">{{trans('navmenu.delivery_note_no')}}</label>
                  <input type="text" class="form-control" id="dn_no" placeholder="{{trans('navmenu.hnt_delivery_note_no')}}" name="delivery_note_no" value="{{$purchase->delivery_note_no}}" />
                </div> 
              </div>
              <div class="col-md-3">
                <div class="form-group pt-2" id="invoice_no">
                  <label for="total" class="form-label">{{trans('navmenu.invoice_no')}}</label>
                  <input type="text"  class="form-control" id="inv_no" placeholder="{{trans('navmenu.hnt_invoice_no')}}" name="invoice_no" value="{{$purchase->invoice_no}}" />
                </div> 
              </div>  
    
              <div class="col-md-3">
                <div class="form-group pt-2">
                  <label class="form-label">{{trans('navmenu.comments')}}</label>
                  <textarea name="comments" class="form-control">@if($purchase->comments != 'null'){{$purchase->comments}}@endif</textarea>
                </div>
              </div>

              <div class="col-md-3">
                <div class="form-group pt-2">
                  <label class="form-label">{{trans('navmenu.amount_paid')}}</label>
                  <input type="text" name="amount_paid" class="form-control" value="{{$purchase->amount_paid}}" readonly>
                </div>
              </div>
            </div>

	            <!-- /.col -->
              <div class="col-md-3 pt-2">
	               <button type="submit" class="btn btn btn-success">{{trans('navmenu.btn_save')}}</button>
                 <a href="javascript:history.back()" class="btn btn-warning">{{trans('navmenu.btn_cancel')}}</a>
              </div>
	        </form>
          </div>
          <!-- /.row -->
        </div>
      </div>
      <!-- /.box -->

@endsection