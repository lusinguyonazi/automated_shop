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
    <div class="row row-cols-1 row-cols-md-1 row-cols-lg-1 row-cols-xl-1">
        <div class="col">
            <h6 class="mb-0 text-uppercase">{{$title}}</h6>
            <hr/>
            <div class="card">
                <div class="card-body">
                    <form class="row g-3 needs-validation" novalidate method="POST" action="{{route('customers.update', encrypt($customer->id))}}">
                        @csrf
                        {{ method_field('PATCH') }}
                        <div class="col-sm-4">
                            <label class="form-label">{{trans('navmenu.customer_name')}} <span style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="text" name="name" required placeholder="{{trans('navmenu.hnt_customer_name')}}" value="{{$customer->name}}" class="form-control form-control-sm mb-3">
                        </div>
                        <div class="col-sm-4">                                
                            <label for="inputEmailAddress" class="form-label">{{trans('navmenu.mobile')}} <span style="color:red">*</span></label>
                            <input type="tel" class="form-control form-control-sm mb-3" name="phone" value="{{$customer->phone}}" placeholder="Eg. 0789XXXXXX" value="{{old('phone')}}">
                            <input type="hidden" name="phone_country" id="countryCode" value="{{$customer->country_code}}">
                            <input type="hidden" name="dial_code" id="dialCode">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label">{{trans('navmenu.email_address')}}</label>
                            <input id="email" type="email" name="email" value="{{$customer->email}}" placeholder="{{trans('navmenu.hnt_customer_email')}}" class="form-control form-control-sm mb-3">
                        </div>
                        <div class="col-sm-4">
                            <label for="address" class="form-label">{{trans('navmenu.postal_address')}}</label>
                            <input id="address" type="text" name="postal_address" placeholder="{{trans('navmenu.hnt_postal_address')}}" value="{{$customer->postal_address}}" class="form-control form-control-sm mb-3">
                        </div>
                        <div class="col-sm-4">
                            <label for="address" class="form-label">{{trans('navmenu.physical_address')}}</label>
                            <input id="address" type="text" name="physical_address" placeholder="{{trans('navmenu.hnt_physical_address')}}" value="{{$customer->physical_address}}" class="form-control form-control-sm mb-3">
                        </div>
                        <div class="col-sm-4">
                            <label for="address" class="form-label">{{trans('navmenu.street')}}</label>
                            <input id="address" type="text" name="street" placeholder="{{trans('navmenu.hnt_street')}}" value="{{$customer->street}}" class="form-control form-control-sm mb-3">
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label">{{trans('navmenu.tin')}}</label>
                            <input id="tin" type="text" name="tin" placeholder="{{trans('navmenu.hnt_customer_tin')}}" value="{{$customer->tin}}" class="form-control form-control-sm mb-3"  data-inputmask='"mask": "999-999-999"' data-mask>
                        </div>
                        <div class="col-sm-4">                                
                            <label class="form-label">{{trans('navmenu.vrn')}}</label>
                            <input id="vrn" type="text" name="vrn" placeholder="{{trans('navmenu.hnt_customer_vrn')}}" value="{{$customer->vrn}}" class="form-control form-control-sm mb-3">
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">{{trans('navmenu.cust_id_type')}}</label>
                            <select class="form-select" name="cust_id_type">
                                @foreach($custids as $cid)
                                @if($cid['id'] == $customer->cust_id_type)
                                <option value="{{$cid['id']}}" selected>{{$cid['name']}}</option>
                                @else
                                <option value="{{$cid['id']}}">{{$cid['name']}}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">{{trans('navmenu.id_number')}}</label>
                            <input type="text" name="custid" value="{{$customer->custid}}" placeholder="{{trans('navmenu.hnt_id_number')}}" class="form-control form-control-sm mb-3">
                        </div>
                                
                        <div class="col-sm-4">
                            <button type="submit" class="btn btn-success" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                            <button type="reset" class="btn btn-default">{{trans('navmenu.btn_reset')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>                
    </div>
    <!--end row-->
@endsection