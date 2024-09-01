@extends('layouts.app')
@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3"></div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{url('home')}}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-xl-12 mx-auto">
            <h6 class="mb-0 text-uppercase" id="new-title">{{$title}}</h6>
            <hr>
            <div class="card">
                <div class="card-body">
                    <div class="p-4 border rounded">
                        <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('services.update', encrypt($service->id)) }}" enctype="multipart/form-data">
                            @csrf
                            {{ method_field('PATCH') }}
                            <div class="col-md-6">
                                <label for="validationCustom01" class="form-label">{{trans('navmenu.service')}}</label>
                                <input type="text" class="form-control" id="validationCustom01" name="name" placeholder="{{trans('navmenu.hnt_enter_service_name')}}" value="{{$service->name}}" required>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please provide a Service name.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="validationCustom02" class="form-label">{{trans('navmenu.price')}}</label>
                                <input type="number" step="any" class="form-control" id="validationCustom02" name="price" placeholder="{{trans('navmenu.hnt_service_price')}}" value="{{$service->pivot->price}}" required>
                                <div class="valid-feedback">Looks good!</div>
                                <div class="invalid-feedback">Please provide a Service Price.</div>
                            </div>
                            <div class="col-md-12">
                                <label for="validationCustom03" class="form-label">{{trans('navmenu.description')}}</label>
                                <input type="tel" class="form-control" id="validationCustom03" name="description" placeholder="{{trans('navmenu.hnt_service_desc')}}" value="{{$service->pivot->description}}">
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary px-4 radius-30" type="submit">{{trans('navmenu.btn_save')}}</button>
                                <button type="button" class="btn btn-warning px-4 radius-30" onclick="showHideForm('hide')">{{trans('navmenu.btn_cancel')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection