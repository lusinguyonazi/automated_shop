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
                <form class="row g-3" method="POST" action="{{route('delivery-notes.update', encrypt($dnote->id))}}">
                    @csrf
                    {{ method_field('PATCH') }}
                    <input type="hidden" name="an_sale_id" value="{{$sale->id}}">
                    <div class="form-group col-md-4">
                        <label>DELIVERY NOTE TO:</label>
                        <input type="text" name="customer" class="form-control" value="{{$sale->name}}" readonly>
                    </div>
                    <div class="form-group col-md-8">
                        <label>Comments: <span style="color: red;">*</span></label>
                        <textarea class="form-control" name="comments" placeholder="Enter Comments here" required>{{$dnote->comments}}</textarea>
                    </div>
                    <div class="form-group col-md-4">
                        <button class="btn btn-primary pull-right" style="margin-left: 5px;">Update</button>
                        <a href="#" onclick="history.back()" class="btn btn-warning pull-right">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection