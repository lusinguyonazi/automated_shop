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
        <div class="col-md-10 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
            <hr/>
            <div class="card">
                <div class="card-body">
                    <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('products.update' ,encrypt($product->id))}}">
                        @csrf
                        @method('PUT')
                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.product_name')}}</label>
                            <input type="text" name="name" class="form-control" value="{{$product->name}}" id="validationCustom01" required>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please provide a Product name.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.basic_unit')}}</label>
                            <select class="form-select form-select-sm" name="basic_unit" style="width: 100%;" id="validationCustom02">
                                <option selected="selected" value="{{$product->basic_unit}}">{{$p_unit}}</option>
                                @foreach($units as $key => $unit)
                                <option value="{{$key}}">{{$unit}}</option>
                                @endforeach
                            </select>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please provide a Product Basic Unit.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="product_no" class="form-label">{{trans('navmenu.product_no')}}</label>
                            <input type="text" name="product_no" class="form-control" value="{{$product->pivot->product_no}}" placeholder="{{trans('navmenu.hnt_product_no')}}">
                        </div>
                        <div class="col-md-6">
                            <label for="location" class="form-label">{{trans('navmenu.location')}}</label>
                            <input  type="text" name="location" placeholder="{{trans('navmenu.hnt_location')}} (Optional)" value="{{$product->pivot->location}}" class="form-control">
                        </div>
                        @if(!$settings->generate_barcode)
                        <div class="col-md-6">
                            <label for="barcode" class="form-label">{{trans('navmenu.barcode_label')}}</label>
                            <input name="barcode" class="form-control" placeholder="Scan/Type Barcode number." type="text" value="{{$product->pivot->barcode}}" />
                        </div>
                        @endif
                        <div class="col-md-6">
                            <label  for="description" class="form-label">{{trans('navmenu.description')}}</label>
                            <textarea name="description" class="form-control" placeholder="{{trans('navmenu.hnt_product_description')}}">@if($product->pivot->description != 'null'){{$product->pivot->description}}@endif</textarea>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn btn-success">{{trans('navmenu.btn_save')}}</button>
                            <a href="javascript:history.back()" class="btn btn-warning">{{trans('navmenu.btn_cancel')}}</a>
                        </div>
                    </form>  
                </div>
            </div>
        </div>
    </div>
@endsection