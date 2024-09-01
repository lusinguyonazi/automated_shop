@extends('layouts.prod')

<meta name="csrf-token" content="{{ csrf_token() }}" />
<script type="text/javascript" src="{{asset('js/angular-1-8-3.min.js')}}"></script>
<script type="text/javascript" src="../js/transOrder.js"></script>
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

<div class="row" >
	<div class="col-md-12">
		<div class="card radius-6">
			<form class="form" method="POST" action="{{route('prod-transfer-store')}}">
				@csrf
				<div class="card-header" >
					<button class="btn btn-success" id="btn-create"><i class="bx bx-file"></i> {{trans('navmenu.create_order')}}</button>
					<a href="{{route('prod-costs.index')}}" class="btn btn-warning " id="btn-create" style="margin-right: 5px;"><i class="bx bx-x"></i> {{trans('navmenu.cancel_order')}}</a>
				</div>
				<div class="card-body">
					<div class="pb-4 row">
						<div class="col-md-4">
							<div class="form-group">
	              <label for="invoice" class="form-label">{{trans('navmenu.date')}}</label>
	              <select name="date_set" id="date_set" onchange="weg(this)" class="form-control form-control-sm">
	                <option value="auto">Auto</option>
	                <option value="manaul">Manual</option>
	              </select>
	            </div>
						</div>
						<div class="col-md-4">
							<div class="form-group" id="date_field" style="display: none;">
								<label class="form-label">{{trans('navmenu.pick_date')}} <span style="color: red; font-weight: bold;">*</span></label>
								<div class="input-group">
									<input type="text" name="order_date" id="datepicker" class="form-control form-control-sm" placeholder="{{trans('navmenu.pick_date')}}">
								</div>
							</div>
						</div>
				
						<div class="col-md-4">
							<div class="form-group">
								<label class="form-label">{{trans('navmenu.destin_shop')}} <span style="color: red; font-weight: bold;">*</span></label>
								<select class="form-control form-control-sm" name="destin_id"  required>
									<option value="{{$shop->id}}" selected>{{$shop->name}}</option>
								</select>
							</div>
						</div>
					</div>
				
					<input type="text" name="production_id" value="{{$production->id}}" hidden="">
			
					<div class="clearfix" style="width: 100%; border-bottom: 2px solid #BBDEFB; margin-bottom: 10px; display: block; overflow: scroll; overflow: auto;"></div>
					
					<div class="col-md-12 order-items">

	                    <table border="0" cellspacing="0" cellpadding="0">
	                    	<thead>
	                    		<tr>
		                    		<th>#</th>
		                    		<th class="Item">{{trans('navmenu.item_name')}}</th>
		                    		<th class="qty">{{trans('navmenu.transfer_qty')}}</th>
		                    		<th class="qty">{{trans('navmenu.source_unit_cost')}}</th>
		                    		<th class="qty">{{trans('navmenu.selling_price')}}</th>
		                    		<th class="qty">{{trans('navmenu.profit_margin')}}</th>
		                    		{{--<th>&nbsp;</th>--}}
	                    		</tr>
	                    	</thead>
	                    	<tbody>
	                    		@foreach($prod_cost_items as $key => $pmitem)
	                    		<tr id="temps">
		                            <td>{{$key + 1}}</td>
		                            <td class="item">{{$pmitem->name}}</td>
		                            <td class="qty">
		                            	<input type="number" style="text-align: center;" min="0" step="any" value="{{$pmitem->quantity}}" class="form-control" readonly="">
		                            </td>
		                            <td class="qty">{{$pmitem->cost_per_unit}}</td>
		                               <td class="qty">
		                            	<input type="number" style="text-align: center;" min="0" step="any" value="{{$pmitem->selling_price}}" class="form-control" readonly="">
		                            </td>
		                            <td class="qty">{{$pmitem->profit_margin}}</td>
 	                              {{--<td><a href="#" ><span class="glyphicon glyphicon-trash" aria-hidden="true" style="color: red"></span></a>
		                            </td> --}}
		                        </tr>
		                        @endforeach
	                    	</tbody>
	                    </table>
	                </div>
				</div>
			</form>
		</div>
	</div>
	
</div>
@endsection