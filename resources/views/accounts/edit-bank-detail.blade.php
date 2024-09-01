@extends('layouts.app')
@section('content')
	<div class="col-md-8">
	<div class="card radius-6">
        <div class="card-body">
        	<form class="form-horizontal" action="{{route('bank-details.update', $bankdetail->id)}}" method="POST">
        		@csrf
        		{{ method_field('PATCH') }}
        		<div class="row pt-2">
        			<label for="street" class="col-sm-4 form-label">Bank Name</label>

                  	<div class="col-sm-8">
                    	<input type="text" class="form-control form-control mb-1" id="bank_name" name="bank_name" placeholder="Bank Name" value="{{$bankdetail->bank_name}}">
                  	</div>
                </div>
                <div class="row pt-2">
                    <label for="street" class="col-sm-4 form-label">Branch Name</label>

                    <div class="col-sm-8">
                      <input type="text" class="form-control form-control mb-1" id="branch_name" name="branch_name" placeholder="Branch Name" value="{{$bankdetail->branch_name}}">
                    </div>
                </div>
                <div class="row pt-2">
                  	<label for="street" class="col-sm-4 form-label">Swift Code</label>

                  	<div class="col-sm-8">
                    	<input type="text" class="form-control form-control mb-1" id="swift_code" name="swift_code" placeholder="Swift Code" value="{{$bankdetail->swift_code}}">
                  	</div>
                </div>
                <div class="row pt-2">
                  	<label for="district" class="col-sm-4 form-label">Account Number</label>

                  	<div class="col-sm-8">
                    	<input type="number" class="form-control form-control mb-1" id="account" name="account_number" placeholder="Account Number" value="{{$bankdetail->account_number}}">
                  	</div>
                </div>

                <div class="row pt-2">
                  	<label for="district" class="col-sm-4 form-label">Account Name</label>

                  	<div class="col-sm-8">
                    	<input type="text" class="form-control form-control mb-1" id="account" name="account_name" placeholder="Account Name" value="{{$bankdetail->account_name}}">
                  	</div>
                </div>
                <div class="pt-2">
	               <button type="submit" class="btn btn btn-success float-end">{{trans('navmenu.btn_save')}}</button>
                   <a href="javascript:history.back()" class="btn btn-warning">{{trans('navmenu.btn_cancel')}}</a>
                </div>
        	</form>
        </div>
    </div>
</div>
@endsection