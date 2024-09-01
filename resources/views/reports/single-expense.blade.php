@extends('layouts.app')

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
                <div class="card-header">
                	<form class="dashform form-horizontal" action="{{url('single-expense-report/'.$type)}}" method="POST">
		            	@csrf
		            	<div class="col-md-6"></div>
			            <input type="hidden" name="start_date" id="start_input" value="">
			            <input type="hidden" name="end_date" id="end_input" value="">
			            <!-- Date and time range -->
			            <div class="col-md-6 float-end">
			              	<div class="input-group">
			                  	<button type="button" class="btn btn-white float-end" id="reportrange">
			                    	<span><i class="bx bx-calendar"></i></span>
			                    	<i class="bx bx-caret-down"></i>
			                  	</button>
			                </div>
			            </div>
			            <!-- /.form group -->
		          	</form>
		        </div>
            	<div class="card-body">
                	<div class="col-xs-12" style="text-align: center; text-transform: uppercase; color: blue; border-bottom: 2px solid #82B1FF; margin-bottom: 8px;">
	                    @if(!is_null($shop->logo_location))
	                   	<figure>
	                    	<img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
	                    </figure>
	                    @endif
	                    <h5>{{$shop->name}}</h5>
	                    <h6>{{trans('navmenu.single_expense_report')}} <br><small>{{trans('navmenu.expense_type')}} : {{$type}}</small> <br><b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></h6>
	                </div>
	                <div class="col-xs-12 table-responsive">
		                <table id="sexpenses" class="table table-responsive table-striped display nowrap" style="width: 100%;">
		                	<thead style="background:#E0E0E0;">
	                          	<tr>
	                            	<th>#</th>
	                            	<th>{{trans('navmenu.date')}}</th>
	                            	<th>{{trans('navmenu.amount')}}</th>
	                            	<th>{{trans('navmenu.description')}}</th>
	                            	@if($settings->is_vat_registered)
	                           		<th>VAT</th>
	                           		@endif
	                           		@if($settings->estimate_withholding_tax)
	                           		<th>{{trans('navmenu.wht_rate')}}</th>
	                           		<th>{{trans('navmenu.wht_amount')}}</th>
	                           		@endif  
	                          	</tr>
	                        </thead>
	                        <tbody>
	                          	@foreach($texpenses as $index => $expense)
	                          	<tr>
	                           		<td>{{$index+1}}</td>
	                           		<td>{{$expense->date}}</td>
	                           		<td>{{number_format($expense->amount)}}</td>
	                           		<td>{{$expense->description}}</td>
	                           		@if($settings->is_vat_registered)
	                           		<td>{{number_format($expense->exp_vat)}}</td>
	                           		@endif
	                           		@if($settings->estimate_withholding_tax)
	                           		<td style="text-align: center;">{{number_format($expense->wht_rate)}} </td>
	                            	<td style="text-align: center;">{{number_format($expense->wht_amount)}} </td>
	                            	@endif
	                          	</tr>
	                          	@endforeach
	                        </tbody>
	                        <tfoot>
	                          	<tr>
	                           		<th></th>
	                           		<th>{{trans('navmenu.total')}}</th>
	                           		<th>{{number_format($total)}}</th>
	                           		<th></th>
				                    @if($settings->is_vat_registered)
				                    <th></th>
				                    @endif
				                    @if($settings->estimate_withholding_tax)
				                    <th></th>
				                    <th></th>
				                    @endif
	                          	</tr>
	                        </tfoot>
	                    </table>
	                </div>
            	</div>
            	<!-- /.tab-content -->
          	</div>
          	<!-- /.nav-tabs-custom -->
        </div>
        <!-- col -->
    </div>
    <!-- row -->
@endsection