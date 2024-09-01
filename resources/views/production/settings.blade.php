@extends('layouts.prod')
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
        <div class="col-xl-8 mx-auto">
            <h6 class="mb-0 text-uppercase">{{trans('navmenu.production_settings')}}</h6>
            <hr>
            <div class="card">
                <div class="card-body">
                    <form class="row g-3" action="{{route('prod-settings.update' , encrypt($settings->id ))}}" method="POST">
                        <!-- Horizontal Form -->
                        @method('PUT')
                        @csrf
                        <div class="row"></div>
                        <div class="col-sm-4 ">
                            <label class="form-label">{{trans('navmenu.disable_prod_panel')}}</label>
                            <select name="disable_prod_panel" class="form-control form-control-sm mb-3">
                                 @if($settings->disable_prod_panel)
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                <option value="0">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="0">{{trans('navmenu.no')}}</option>
                                <option value="1">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                    
                        <div class=" row">
                            <button type="submit" class="btn btn-primary col-sm-2">{{trans('navmenu.btn_save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection