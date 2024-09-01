@extends('layouts.app')

@section('content')
	
	<!-- SELECT2 EXAMPLE -->
    <div class="box box-default">
        <div class="box-header with-border">
          	<h3 class="box-title">{{$cashin->source}}</h3>

          	<div class="box-tools pull-right">
            	<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            	<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-remove"></i></button>
          	</div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          	<div class="row">

        		<form class="form-validate" method="POST" action="{{route('cash-ins.update', Crypt::encrypt($cashin->id))}}">
        			@csrf
            		{{ method_field('PATCH') }}
	            	<div class="col-md-6">
	              		<div class="form-group">
	                		<label>{{trans('navmenu.source')}}</label>
	                		<select class="form-control" name="source" required>
	                    		<option>{{$cashin->source}}</option>
	                    		@foreach($cins as $key => $value)
	                    		<option>{{$value->source}}</option>
	                    		@endforeach
	                  		</select>
	              		</div>
	            	</div>
              		<div class="col-md-6">
                		<div class="form-group">
                  			<label class="control-label">{{trans('navmenu.account')}} <span style="color: red; font-weight: bold;">*</span></label>
                  			<select class="form-control select2" name="account" required style="width: 100%;">
                    			<option>{{$cashin->account}}</option>
                    			<option value="Cash">{{trans('navmenu.cash')}}</option>
                    			<option value="Bank">{{trans('navmenu.bank')}}</option>
                    			<option value="Mobile Money">{trans('navmenu.mobilemoney')}</option>
                  			</select>
                		</div>
              		</div>
              		<div class="col-md-6">
                		<div class="form-group">
                  			<label>{{trans('amount')}}</label>
                  			<input type="text" name="amount" class="form-control" value="{{$cashin->amount}}">
                		</div>
              		</div>
              		<div class="form-group">
                		<label>{{trans('navmenu.date')}}</label>
                		<div class="input-group date">
                  			<div class="input-group-addon">
                    		<i class="fa fa-calendar"></i>
                  			</div>
                  			<input type="text" name="in_date" id="in_date" value="{{$cashin->in_date}}" placeholder="Choose date" class="form-control">
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
            var $min = document.querySelector('[name="in_date"]'),
                $max = document.querySelector('[name="indate"]');


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