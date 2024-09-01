@extends('layouts.prod')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('/prod-home')}}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="col-md-10 mx-auto">
        <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
        <hr>
        <div class="card radius-6">
            <div class="card-body">
                <form class="form" method="POST" action="{{route('raw-materials.update', encrypt($material->id))}}">
                    @csrf
                    {{ method_field('PUT') }}
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.material_name')}} <span style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="text" name="name" required placeholder="{{trans('navmenu.hnt_product_name')}}" class="form-control form-control-sm mb-3" value="{{ $material->name }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.basic_unit')}} <span style="color: red; font-weight: bold;">*</span></label>
                                <select class="form-control form-select-sm mb-3" name="basic_unit" required style="width: 100%;">
                                    @foreach($units as $key => $unit)
                                    <option value="{{$key}}" @if ($material->basic_unit === $key) selected @endif>{{$unit}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                         <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.current_stock')}}</label>
                                <input id="qty" type="number" min="0" name="in_store" step="any" placeholder="{{trans('navmenu.hnt_current_stock')}}" class="form-control form-control-sm mb-3" value="{{ $material->pivot->in_store }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.buying_per_unit')}}</label>
                                <input id="unit_price" type="number" min="0" step="any" name="unit_cost" placeholder="{{trans('navmenu.hnt_buying_price')}}" class="form-control form-control-sm mb-3" value="{{ $material->pivot->unit_cost }}">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.description')}}</label>
                                <textarea name="description" class="form-control form-control-sm mb-3" placeholder="{{trans('navmenu.hnt_product_desc')}}">{{ $material->pivot->description }}</textarea>
                            </div>
                        </div> 

                        <div class="col-md-12">
                            <div class="form-group float-end">
                                <button type="submit" class="btn btn-success">{{trans('navmenu.btn_save')}}</button>
                                <a href="javascript:history.back()" class="btn btn-warning">{{trans('navmenu.btn_cancel')}}</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection


