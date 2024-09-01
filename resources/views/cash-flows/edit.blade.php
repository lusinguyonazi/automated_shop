@extends('layouts.app')
@section('content')
	<!-- SELECT2 EXAMPLE -->
    <div class="card radius-6">
        <div class="card-header">
          	<h4>{{$cashout->reason}}</h4>
        </div>
        <!-- /.box-header -->
        <div class="card-body">
          	<div class="row">
        		<form class="form-validate" method="POST" action="{{route('cash-flows.update', encrypt($cashout->id))}}">
        			@csrf
            		{{ method_field('PATCH') }}
                    <div class="row">
    	            	<div class="col-md-6">
    	                	<label class="form-label">{{trans('navmenu.reason')}}</label>
    	                	<select class="form-control" name="reason" required>
    	                    	<option>{{$cashout->reason}}</option>
    	                    	@foreach($couts as $key => $value)
    	                    	<option>{{$value->reason}}</option>
    	                    	@endforeach
    	                  	</select>
    	            	</div>
                  		<div class="col-md-6">
                      		<label class="form-label">{{trans('navmenu.account')}} <span style="color: red; font-weight: bold;">*</span></label>
                      		<select class="form-control select2" name="account" required style="width: 100%;">
                        		<option>{{$cashout->account}}</option>
                        		<option value="Cash">{{trans('navmenu.cash')}}</option>
                        		<option value="Bank">{{trans('navmenu.bank')}}</option>
                        		<option value="Mobile Money">{trans('navmenu.mobilemoney')}</option>
                      		</select>
                  		</div>
                    </div>
                    <div class="row">
                  		<div class="col-md-6">
                      		<label class="form-label">{{trans('amount')}}</label>
                      		<input type="text" name="amount" class="form-control" value="{{$cashout->amount}}">
                  		</div>
                  		<div class="col-md-6">
                    		<label class="form-label">{{trans('navmenu.date')}}</label>
                    		<div class="input-group date">
                      			<div class="input-group-text">
                        		<i class="bx bx-calendar"></i>
                      			</div>
                      			<input type="text" name="out_date" id="out_date" value="{{$cashout->out_date}}" placeholder="Choose date" class="form-control">
                    		</div>
                  		</div>
                    </div>
	           	 	<!-- /.col -->
              		<div class="col-md-6">
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

<link rel="stylesheet" href="../../css/DatePickerX.css">
<script src="../../js/DatePickerX.min.js"></script>
<script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="out_date"]'),
                $max = document.querySelector('[name="outdate"]');


            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                // minDate    : new Date(),
                maxDate    : new Date()
            });

        });
</script>