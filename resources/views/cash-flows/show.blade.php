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
        <div class="col-xl-5 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
            <hr/>
            <div class="card radius-6">
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{trans('navmenu.date')}}</b> <a class="pull-right">{{$cashout->out_date}}</a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{trans('navmenu.amount')}}</b> <a class="pull-right">{{$cashout->amount}}</a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{trans('navmenu.paid')}}</b> <a class="pull-right">{{$cashout->amount_paid}}</a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{trans('navmenu.reason')}}</b> <a class="pull-right">{{$cashout->reason}}</a>
                        </li>
                        @if($cashout->is_borrowed === 1)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{trans('navmenu.is_borrowed')}}</b> <a class="pull-right">{{trans('navmenu.yes')}}</a>
                        </li>
                        @else
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{trans('navmenu.is_borrowed')}}</b> <a class="pull-right">{{trans('navmenu.no')}}</a>
                        </li>
                        @endif
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{trans('navmenu.status')}}</b> <a class="pull-right">{{$cashout->status}}</a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>{{trans('navmenu.customer')}}</b> <a class="pull-right">{{$customer}}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection