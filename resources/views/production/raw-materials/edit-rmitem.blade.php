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
                <form class="form-validate" method="POST" action="{{route('rm-items.update', $rmitem->id)}}">
                    @csrf
                    {{ method_field('PATCH') }} 
                    <div class="row">
                        <input type="hidden" name="id" value="{{$rmitem->id}}">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.quantity')}}</label>
                                <input type="number" min="1" name="qty" class="form-control" value="{{$rmitem->qty}}">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.unit_cost')}}</label>
                                <input type="number" step="any" name="unit_cost" class="form-control" value="{{$rmitem->unit_cost}}">
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="form-group">
                                <button type="submit" class="btn btn btn-success">{{trans('navmenu.btn_save')}}</button>
                                <a href="javascript:history.back()" class="btn btn-warning">{{trans('navmenu.btn_cancel')}}</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection


