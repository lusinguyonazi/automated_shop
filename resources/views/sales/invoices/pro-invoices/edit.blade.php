@extends('layouts.app')
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
 <script type="text/javascript">
 	function saveItemChanges(key) {
        document.getElementById('item-form-'+key).submit();
 	}

 	function saveChanges1(key) {
 		document.getElementById('item1-form-'+key).submit();
 	}

 	function saveServChanges1(key) {
        document.getElementById('service1-form-'+key).submit();
 	}

 	function saveServChanges(key) {
        document.getElementById('service-form-'+key).submit();
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
        <div class="col-md-11 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
            <hr/>
            <div class="card">
            	<div class="card-body">
					<div class="clearfix invoice-header">
						@if(!is_null($shop->logo_location))
						<figure>
							<img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
						</figure>
						@endif
						<div class="company-address">
							<h6 class="title">{{$shop->name}}</h6>
							<p>
								{{$shop->postal_address}} {{$shop->street}} {{$shop->district}},<br>
								{{$shop->city}}
							</p>
						</div>
							<div class="company-contact">
							Mobile: <a href="#">{{$shop->mobile}}</a><br>
							E-Mail: <a href="#">{{$shop->email}}</a>
						</div>
					</div>

					<div class="invoice-content">
						<div class="details clearfix">
							<div class="client float-start">
								<p>INVOICE TO: {{$customer->name}}</p>
								<p>{{$customer->address}}</p>
								<a href="#">{{$customer->email}}</a>
								<p>
			                    	<button style="width: 50%" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#customerModal">Edit Customer Info</button>
		                    		
		                    		<form style="width: 50%" method="POST" action="{{url('change-customer')}}">
							            @csrf
							            <input type="hidden" name="invoice_id" value="{{$invoice->id}}">
							            <div class="form-group">
							            	<select class="form-control select2" onchange='if(this.value != 0) { this.form.submit(); }' name="customer_id" required>
							            		<option value="">Change Customer</option>
							            		@foreach($customers as $key => $cust)
						                        <option value="{{$key}}">{{$cust}}</option>
						                        @endforeach
							            	</select>
							            </div>
						        	</form>
		                    	</p>
							</div>
							<div class="data float-end">
								<div class="title">Proforma Invoice </div>
								<div class="date">
									Date of Invoice: {{date("F d, Y", strtotime($invoice->time_created))}}<br>
									Due Date: {{$invoice->due_date}}
								</div>
								<p>
									<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#genInvoiceModal">General Invoice</button>
								</p>
							</div>
						</div>
						<div class="form-group">
							@if($shop->business_type_id == 3)
							<form style="width: 50%" method="POST" action="{{url('add-invocie-servitem')}}">
								@csrf
								<label class="form-label"><i class="bx bx-plus"></i> Add Item</label>
								<input type="hidden" name="invoice_id" value="{{$invoice->id}}">
								<select class="form-control form-control-sm mb-3 select2" onchange='if(this.value != 0) { this.form.submit(); }' name="service_id" required>
									<option value="">Select Service</option>
								    @foreach($services as $key => $service)
							        <option value="{{$service->id}}">{{$service->name}}</option>
							        @endforeach
								</select>
							</form>
							@elseif($shop->business_type_id == 4)
							<div><span><i class="bx bx-plus"></i> Add Item</span></div>
							<div class="row" style="padding-bottom: 4;">
								<div class="col-md-6">
									<form method="POST" action="{{url('add-invoice-item')}}">
										@csrf
										<input type="hidden" name="invoice_id" value="{{$invoice->id}}">
									    <select class="form-control form-control-sm mb-3 select2" onchange='if(this.value != 0) { this.form.submit(); }' name="product_id" required>
									     	<option value="">Select Product</option>
									     	@foreach($products as $key => $product)
								            <option value="{{$product->id}}">{{$product->name}}</option>
								            @endforeach
									    </select>
									</form>
								</div> 
								<div class="col-md-6">
									<form method="POST" action="{{url('add-invocie-servitem')}}">
										@csrf 
										<input type="hidden" name="invoice_id" value="{{$invoice->id}}">
								    	<select class="form-control form-control-sm mb-3 select2" onchange='if(this.value != 0) { this.form.submit(); }' name="service_id" required>
								     		<option value="">Select Service</option>
								     		@foreach($services as $key => $service)
								           	<option value="{{$service->id}}">{{$service->name}}</option>
								           	@endforeach
								    	</select>
								    </form>
								</div>
							</div>
						    @else
						    <label><i class="bx bx-plus"></i> Add Item</label>
							<form style="width: 50%" method="POST" action="{{url('add-invoice-item')}}">
							   	@csrf
							    <input type="hidden" name="invoice_id" value="{{$invoice->id}}">
								<select class="form-control form-control-sm mb-3 select2" onchange='if(this.value != 0) { this.form.submit(); }' name="product_id" required>
							     	<option value="">Select Product</option>
							     	@foreach($products as $key => $product)
						            <option value="{{$product->id}}">{{$product->name}}</option>
						            @endforeach
								</select>
							</form>
						    @endif
						</div>
						@if($shop->business_type_id == 3)
						<table border="0" cellspacing="0" cellpadding="0">
							<thead>
								<tr>
									<th class="desc">Description</th>
									<th class="qty">Quantity</th>
									<th class="unit">Unit price</th>
									<th class="total">Total</th>
									<th class="del">Delete</th>
								</tr>
							</thead>
							<tbody>
								@foreach($servitems as $key => $item)
								<tr style="border-bottom: 1px solid #e0e0e0;">	
									<td class="desc">
										{{$item->name}} {{$item->description}}
									</td>
									<form id="service1-form-{{$key}}" class="form" method="POST" action="{{url('update-invoice-item')}}">
										<td class="qty">
											@csrf
											<input type="hidden" name="id" value="{{$item->id}}">
											<input type="hidden" name="invoice_id" value="{{$invoice->id}}">
											<input id="input_qty1{{$key}}" type="number" name="repeatition" value="{{$item->repeatition}}" onblur="saveServChanges1('<?php echo $key; ?>')">
										</td>
									</form>
									<td class="unit">{{$item->cost_per_unit}}</td>
									<td class="total">{{$item->amount}}</td>
									<td class="del">
										<a href="{{url('delete-invoice-servitem/'.Crypt::encrypt($item->id))}}"><i class="bx bx-trash"  style="color: red;"></i></a>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>

						@elseif($shop->business_type_id == 4)
						<table border="0" cellspacing="0" cellpadding="0">
							<thead>
								<tr>
									<th class="desc">Description</th>
									<th class="qty">Quantity</th>
									<th class="unit">Unit price</th>
									<th class="total">Total</th>
									<th class="del">Delete</th>
								</tr>
							</thead>
							<tbody>
								@foreach($items as $ky => $item)
								<tr style="border-bottom: 1px solid #e0e0e0;">	
									<td class="desc">
										{{$item->name}} {{$item->description}}
									</td>

									<form id="item-form-{{$ky}}" class="form" method="POST" action="{{url('update-invoice-item')}}">
										<td class="qty">
											@csrf
											<input type="hidden" name="id" value="{{$item->id}}">
											<input type="hidden" name="invoice_id" value="{{$invoice->id}}">
											<input id="input_qty2{{$ky}}" type="number" name="quantity" value="{{$item->quantity}}" onblur="saveItemChanges('<?php echo $key; ?>')">
										</td>
									</form>
									<td class="unit">{{$item->cost_per_unit}}</td>
									<td class="total">{{$item->amount}}</td>
									<td class="del">
										<a href="{{url('delete-invoice-item/'.Crypt::encrypt($item->id))}}"><i class="bx bx-trash"  style="color: red;"></i></a>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>

						<table border="0" cellspacing="0" cellpadding="0">
							<thead>
								<tr>
									<th class="desc">Description</th>
									<th class="qty">Quantity</th>
									<th class="unit">Unit price</th>
									<th class="total">Total</th>
									<th class="del">Delete</th>
								</tr>
							</thead>
							<tbody>
								@foreach($servitems as $index => $item)
								<tr style="border-bottom: 1px solid #e0e0e0;">	
									<td class="desc">
										{{$item->name}} {{$item->description}}
									</td>

									<form id="service-form-{{$index}}" class="form" method="POST" action="{{url('update-invoice-item')}}">
										<td class="qty">
											@csrf
											
											<input type="hidden" name="id" value="{{$item->id}}">
											<input type="hidden" name="invoice_id" value="{{$invoice->id}}">
											<input id="input_qty3{{$index}}" type="number"  name="repeatition" value="{{$item->repeatition}}" onblur="saveServChanges('<?php echo $index; ?>')">
										</td>
									</form>
									<td class="unit">{{$item->cost_per_unit}}</td>
									<td class="total">{{$item->amount}}</td>
									<td class="del">
										<a href="{{url('delete-invoice-servitem/'.Crypt::encrypt($item->id))}}"><i class="bx bx-trash"  style="color: red;"></i></a>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
						@else
						<table border="0" cellspacing="0" cellpadding="0">
							<thead>
								<tr>
									<th class="desc">Description</th>
									<th class="qty">Quantity</th>
									<th class="unit">Unit price</th>
									<th class="total">Total</th>
									<th class="del">Delete</th>
								</tr>
							</thead>
							<tbody>
								@foreach($items as $key => $item)
								<tr style="border-bottom: 1px solid #e0e0e0;">	
									<td class="desc">
										{{$item->name}} {{$item->description}}
									</td>

									<form id="item1-form-{{$key}}" class="form" method="POST" action="{{url('update-invoice-item')}}">
										<td class="qty">
											@csrf
											<input type="hidden" name="id" value="{{$item->id}}">
											<input type="hidden" name="invoice_id" value="{{$invoice->id}}">
											<input id="input_qty4{{$key}}" type="number" name="quantity" value="{{$item->quantity}}" onblur="saveChanges1('<?php echo $key; ?>')">
										</td>
									</form>
									<td class="unit">{{$item->cost_per_unit}}</td>
									<td class="total">{{$item->amount}}</td>
									<td class="del">
										<a href="{{url('delete-invoice-item/'.encrypt($item->id))}}"><i class="bx bx-trash"  style="color: red;"></i></a>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
						@endif
						<div class="no-break">
							<table class="grand-total">
								<tbody>
									<tr>
										<td class="desc"></td>
										<td class="qty"></td>
										<td class="unit" style="border-bottom: 1px solid #e0e0e0;">SUBTOTAL:</td>
										<td class="total" style="border-bottom: 1px solid #e0e0e0;">{{number_format($subtotal)}}</td>
									</tr>
									<tr>
										<td class="desc"></td>
										<td class="qty"></td>
										<td class="unit" style="border-bottom: 1px solid #e0e0e0;">DISCOUNT:</td>
										<td class="total" style="border-bottom: 1px solid #e0e0e0;">{{number_format($invoice->discount)}}</td>
									</tr>
									<tr>
										<td class="desc"></td>
										<td class="qty"></td>
										<td class="unit" style="border-bottom: 1px solid #e0e0e0;">TAX 18%:</td>
										<td class="total" style="border-bottom: 1px solid #e0e0e0;">{{number_format($tax)}}</td>
									</tr>
									<tr>
										<td class="desc"></td>
										<td class="qty"></td>
										<td class="unit" style="border-bottom: 1px solid #e0e0e0;">SHIPPING COSTS</td>
										<td class="total" style="border-bottom: 1px solid #e0e0e0;">{{number_format($invoice->shipping_cost)}}</td>
									</tr>
									<tr>
										<td class="desc"></td>
										<td class="qty"></td>
										<td class="unit" style="border-bottom: 1px solid #e0e0e0;">ADJUSTMENT:</td>
										<td class="total" style="border-bottom: 1px solid #e0e0e0;">{{number_format($invoice->adjustment)}}</td>
									</tr>
									<tr>
										<td class="desc"></td>
										<td class="unit" colspan="2" style="border-bottom: 1px solid #e0e0e0;">GRAND TOTAL:</td>
										<td class="total" style="border-bottom: 1px solid #e0e0e0;">{{number_format(($grandtotal+$tax+$invoice->shipping_cost+$invoice->adjustment)-$invoice->discount, 2, '.', ',')}}</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="invoice-footer">
						<div class="thanks">Thank you!</div>
						<div class="notice">
							<div>NOTICE:</div>
							<div>{{$invoice->notice}}</div>
						</div>
						<div class="end">This is an electronic Invoice and is valid without the signature and seal.</div>
					</div>
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
            	<div class="modal-body row">
            		@csrf
	                <div class=" col-md-6">
	                      <label class="form-label">{{trans('navmenu.customer_name')}} <span style="color: red; font: bold;">*</span></label>
	                      <input id="register-username" type="text" name="name" required placeholder="{{trans('navmenu.hnt_customer_name')}}" class="form-control form-control-sm mb-3" value="{{$customer->name}}">
	                </div>
	                <div class=" col-md-6">
	                      <label class="form-label">{{trans('navmenu.phone_number')}}</label>
	                      <input id="register-username" type="text" name="phone" placeholder="{{trans('navmenu.hnt_customer_mobile')}}" class="form-control form-control-sm mb-3"  data-inputmask='"mask": "9999999999"' data-mask value="{{$customer->phone}}">
	                </div>
	                <div class=" col-md-6">
	                      <label class="form-label">{{trans('navmenu.email_address')}}</label>
	                      <input id="register-email" type="text" name="email" placeholder="{{trans('navmenu.hnt_customer_email')}}" class="form-control form-control-sm mb-3">
	                </div>
	                <div class=" col-md-6">
	                    <label class="form-label">{{trans('navmenu.postal_address')}}</label>
	                    <input id="address" type="text" name="postal_address" placeholder="{{trans('navmenu.hnt_postal_address')}}" class="form-control form-control-sm mb-3" value="{{$customer->postal_address}}">
	                </div>
	                <div class=" col-md-6">
	                    <label class="form-label">{{trans('navmenu.physical_address')}}</label>
	                    <input id="address" type="text" name="physical_address" placeholder="{{trans('navmenu.hnt_physical_address')}}" class="form-control form-control-sm mb-3" value="{{$customer->physical_address}}">
	                </div>
	                <div class=" col-md-6">
	                    <label class="form-label">{{trans('navmenu.street')}}</label>
	                    <input id="address" type="text" name="street" placeholder="{{trans('navmenu.hnt_street')}}" class="form-control form-control-sm mb-3">
	                </div>
	                <div class=" col-md-6">
	                      <label class="form-label">{{trans('navmenu.tin')}}</label>
	                      <input id="register-username" type="text" name="tin" placeholder="{{trans('navmenu.hnt_customer_tin')}}" class="form-control form-control-sm mb-3" value="{{$customer->tin}}" data-inputmask='"mask": "999-999-999"' data-mask>
	                </div>
	                <div class=" col-md-6">
	                      <label class="form-label">{{trans('navmenu.vrn')}}</label>
	                      <input id="register-username" type="text" name="vrn" placeholder="{{trans('navmenu.hnt_customer_vrn')}}" class="form-control form-control-sm mb-3" value="{{$customer->vrn}}">
	                </div>
	                 <div class="col-sm-6">
	                    <label class="form-label">{{trans('navmenu.cust_id_type')}}</label>
	                    <select class="form-select form-select-sm mb-3" name="cust_id_type">
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
	            <div class="modal-footer">
                	<button type="submit" class="btn btn btn-success">{{trans('navmenu.btn_save')}}</button>
                	<button type="button" class="btn btn-warning" data-bs-dismiss="modal">{{trans('navmenu.btn_cancel')}}</button>
            	</div>
        	</form>
        </div>
    </div>
</div>

 <!-- Modal -->
<div class="modal fade" id="genInvoiceModal" tabindex="-1" role="dialog" aria-bs-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            	<h5 class="modal-title" id="myModalLabel">General Invoice Info</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                
            </div>
	        <form class="form-validate" method="POST" action="{{ route('pro-invoices.update', encrypt($invoice->id))}}">
	            <div class="modal-body row">
	            	@csrf  
              		{{ method_field('PATCH') }} 
              		<div class="col-md-6">
			            <label for="due_date" class="form-label">Due Date</label>
			            <div class="inner-addon left-addon">
			                <i class="myaddon bx bx-calendar"></i>
			                <input type="text" id="duedatepicker" name="due_date" placeholder="Choose Due date" class="form-control form-control-sm mb-3" value="{{$invoice->due_date}}" required>
			            </div>
			        </div>
			        <div class="col-md-6">
			           	<label for="total" class="form-label">Discount</label>
			           	<input type="number" style="text-align:left;" name="discount" class="form-control form-control-sm mb-3" min="0" value="{{$invoice->discount}}">
			        </div>
			        <div class="col-md-6">
			        	<label for="total" class="form-label">Shipping Costs</label>
			            <input type="number" style="text-align:left;" name="shipping_cost" class="form-control form-control-sm mb-3" min="0" value="{{$invoice->shipping_cost}}">
			       	</div>
			       	<div class="col-md-6">
			            <label for="total" class="form-label">Adjustment</label>
			           	<input type="number" style="text-align:left;" name="adjustment" class="form-control form-control-sm mb-3" value="{{$invoice->adjustment}}">
			        </div>
			        <div class="col-md-12">
			        	<label for="employee"class="form-label">Notice</label>
			           	<textarea  class="form-control form-control-sm mb-3" name="notice" id="notice" >{{$invoice->notice}}</textarea>
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
<link rel="stylesheet" href="{{asset('css/DatePickerX.css')}}">
<script src="{{asset('js/DatePickerX.min.js')}}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $max = document.querySelector('[name="due_date"]');
            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                minDate    : new Date()
            });
        });
    </script>