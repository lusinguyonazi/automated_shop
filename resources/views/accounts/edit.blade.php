@extends('layouts.app')
@section('content')
	<div class="card radius-6">
			<form class="form-validate" method="POST" action="{{route('user-profile.update', encrypt($user->id))}}">
	            <div class="card-body">
	            	@csrf
	            	{{ method_field('PATCH') }}
	            	<input type="hidden" name="id" value="{{$user->id}}">
	            	<div class="row">
		            	<div class="col-md-6 pt-3">
		              		<label for="register-username" class="form-label">{{trans('navmenu.first_name')}}</label>
		              		<input id="register-username" type="text" name="first_name" required data-msg="Please enter your full name" class="form-control" value="{{$user->first_name}}">
		              
		            	</div>
		            	<div class=" col-md-6 pt-3">
		              		<label for="register-username" class="form-label">{{trans('navmenu.last_name')}}</label>
		              		<input id="register-username" type="text" name="last_name" required data-msg="Please enter your Last name" class="form-control" value="{{$user->last_name}}">
		            	</div>

		            	<div class="col-md-6 pt-3">
	                  		<label class="form-label">{{trans('navmenu.country')}}</label>
	                    		<select class="form-control select2" name="country_code" id="sel_ctr">
	                    			<option value="TZ">Tanzania</option>
	                    		</select>
	                	</div>
		            	<div class="col-md-6 pt-3">
		              		<label for="register-phone" class="form-label">{{trans('navmenu.mobile')}}</label>
		              		<input id="register-phone" type="tel" name="phone" required data-msg="Please enter a valid phone number" class="form-control" value="{{$user->phone}}">
		            	</div>

		            	<div class="col-md-6 pt-3">
		              		<label for="register-email" class="form-label">{{trans('navmenu.email_address')}} </label>
		              		<input id="register-email" type="email" name="email" data-msg="Please enter a valid email address" class="form-control" value="{{$user->email}}">
		            	</div>

						{{-- <div class="col-md-6 pt-3">
						<a href="{{url('change-password')}}" class="btn btn-warning col-12"><i class="bx bx-key"></i><b> {{trans('navmenu.change_password')}}</b></a>

						</div> --}}
		            	<div class="col-md-12 pt-3">
		                	<button type="submit" class="btn btn btn-success">{{trans('navmenu.btn_save')}}</button>
		                	<a href="javascript:history.back()" class="btn btn-warning">{{trans('navmenu.btn_cancel')}}</a>
		            	</div>
		            </div>
		            </div>
		        </form>
		    </div>
@endsection