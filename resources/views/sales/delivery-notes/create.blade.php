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
                @if($shop->subscription_type_id >= 2)
                <form class="row g-3" method="POST" action="{{route('delivery-notes.store')}}">
                    @csrf
                    <input type="hidden" name="an_sale_id" value="{{$sale->id}}">
                    <div class="form-group col-md-4">
                        <label>DELIVERY NOTE TO:</label>
                        <input type="text" name="customer" class="form-control" value="{{$sale->name}}" readonly>
                    </div>
                    <div class="form-group col-md-8">
                        <label>Comments: <span style="color: red;">*</span></label>
                        <textarea class="form-control" name="comments" placeholder="Enter Comments here" required></textarea>
                    </div>
                    <div class="form-group col-md-4">
                        <button class="btn btn-primary pull-right" style="margin-left: 5px;">Create</button>
                        <a href="#" onclick="history.back()" class="btn btn-warning pull-right">Cancel</a>
                    </div>
                </form>
                @else
                <div class="text-center">
                    <h3 style="color: blue;">This is Premium Feautere.</h3>
                    <h4>Upgrade to Premium Version to get More advanced features by opening Settings page or click <a href="{{url('settings')}}">here</a></h4>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection