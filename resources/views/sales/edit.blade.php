@extends('layouts.app')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('admin/home')}}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">

        </div>
    </div>
    <!--end breadcrumb-->
    <div class="col-xl-9 mx-auto">
        <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
        <hr/>
        <div class="card">
            <div class="card-body">
                <form class="row g-3" method="POST" action="{{ route('an-sales.update', encrypt($sale->id))}}">
                    @csrf
                    {{ method_field('PATCH') }}
                    <input type="hidden" name="id" value="{{$sale->id}}">
                    <div class="col-md-6">
                        <label>{{trans('navmenu.customer_name')}}</label>
                        <select class="form-control select2" name="customer_id" required>
                            <option value="{{$customer->id}}">{{$customer->name}}</option>
                            @foreach($customers as $customer)
                            <option value="{{$customer->id}}">{{$customer->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="control-label">{{trans('navmenu.saledate')}}</label>
                        <div class="inner-addon left-addon">
                            <i class="myaddon bx bx-calendar"></i>
                            <input type="text" name="sale_date" id="sale_date" placeholder="{{trans('navmenu.pick_date')}}" value="{{$sale->time_created}}" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label>{{trans('navmenu.amount_paid')}}</label>
                        <input type="number" min="0" name="amount_paid" class="form-control" value="{{$sale->sale_amount_paid}}" readonly>
                    </div>

                    @if($settings->is_service_per_device)
                        @if(!is_null($dsale))
                        <div class="col-md-6">
                            <label class="control-label">{{trans('navmenu.device_number')}}</label> 
                            <select name="device_id" class="form-control">
                                <option value="{{App\Device::find($dsale->device_id)->id}}">{{App\Device::find($dsale->device_id)->device_number}}</option>
                                @foreach($devices as $device)
                                <option value="{{$device->id}}">{{$device->device_number}}</option>
                                @endforeach
                            </select>
                        </div>
                        @else
                        <div class="col-md-6">
                            <label class="control-label">{{trans('navmenu.device_number')}}</label>
                            <select name="device_id" class="form-control select2">
                                <option value="">{{trans('navmenu.select_device')}}</option>
                                @foreach($devices as $device)
                                <option value="{{$device->id}}">{{$device->device_number}}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    @endif
                    <div class="col-md-6">
                        <label>{{trans('navmenu.comments')}}</label>
                        <textarea name="comments" class="form-control">{{$sale->comments}}</textarea>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn btn-success">{{trans('navmenu.btn_save')}}</button>
                        <a href="javascript:history.back()" class="btn btn-warning">{{trans('navmenu.btn_cancel')}}</a> 
                    </div>
                </form>
            </div>
        </div>                
    </div>
    <!--end row-->
@endsection

<link rel="stylesheet" href="{{asset('css/DatePickerX.css')}}">
<script src="{{asset('js/DatePickerX.min.js')}}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="sale_date"]');

            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : d,
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>