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
    <div class=" col-md-9 mx-auto"> 
        <h3>{{$title}}</h3>
        <hr>
        <div class="card">
            <div class="card-body">
                <form class="row g-3" method="POST" action="{{route('product-units.update', encrypt($prod_unit->id))}}">
                    @csrf
                    {{ method_field('PATCH') }}
                    <div class="col-sm-4">
                        <label class="form-label">Unit</label>
                        <select class="form-select select2" name="unit_name" required>
                            <option value=""> ---Select--</option>
                            @foreach($units as $key => $unit)
                                @if($prod_unit->unit_name == $key)
                                <option value="{{$key}}" selected>{{$unit}}</option>
                                @else
                                <option value="{{$key}}">{{$unit}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label">Qty equivalent to Basic Unit</label>
                        <input class="form-control form-control-sm mb-3" type="number" name="qty_equal_to_basic" placeholder="Enter quantity" value="{{$prod_unit->qty_equal_to_basic}}" required>
                    </div>
                    <div class="col-sm-4">
                        <label class="form-label">Unit Price</label>
                        <input class="form-control form-control-sm mb-3" type="number" name="unit_price" placeholder="Enter Unit Price" value="{{$prod_unit->unit_price}}" required>
                    </div>
                    <div class="col-sm-6">
                        <button class="btn btn-primary pull-right" style="margin-left: 5px;">Update</button>
                        <a href="#" onclick="history.back()" class="btn btn-warning pull-right">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection