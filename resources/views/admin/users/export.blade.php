@extends('layouts.adm')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('admin/home')}}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $title }} From <span class="text-primary">{{ $duration['from'] }}</span> To <span
                                class="text-primary">{{ $duration['to'] }} </span>
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-md-12 mx-auto">
            <form class="dashform row g-3" action="{{ url('admin/export-shops') }}" method="get">
                @csrf
                <div class="col-md-3"></div>
                <div class="col-md-2"></div>
                <input type="hidden" name="start_date" id="start_input" value="">
                <input type="hidden" name="end_date" id="end_input" value="">
                <!-- Date and time range -->
                <div class="col-md-7 mb-1">
                    <div class="input-group d-flex justify-content-end">
                        <input class="form-control form-control mx-3" account="text" style="width: 45px;" name="username"
                            placeholder="Enter Username" id="userinput8" required>
                        <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange"><span><i
                                    class="bx bx-calendar"></i></span><i class="fa fa-caret-down"></i></button>
                    </div>
                </div>
            </form>
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-success" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" href="{{ url('/admin/shops') }}" role="tab" aria-selected="true">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-list-plus font-18 me-1'></i></div>
                                    <div class="tab-title">Shops</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" href="{{ url('/admin/export-shops') }}" role="tab" aria-selected="true">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-export font-18 me-1'></i></div>
                                    <div class="tab-title">Export Shops</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <!-- /.tab-pane -->
                        <div class="tab-pane fade show active" id="tab_1-1" role="tabpanel">
                            <table id="example3" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                <thead style="font-weight: bold; font-size: 14;">
                                    <tr>
                                        <th style="width: 10px;">#</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Mobile</th>
                                        <th>Shop Name</th>
                                        <th>Date registered</th>
                                        <th>Is Default?</th>
                                        <th>Expire Date</th>
                                        <th>Is Expired?</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($usershops as $key => $shop)
                                    <tr>
                                        <td>{{ $key+1  }}</td>
                                        <td>{{ $shop->first_name }}</td> 
                                        <td>{{ $shop->last_name}} </td>
                                        <td>{{ $shop->phone }}</td>
                                        <td>{{ $shop->name }} </td>
                                        <td>{{ $shop->created_at}} </td>
                                        <td>{{ $shop->is_default}}</td>
                                        <td>{{ $shop->expire_date }}</td>
                                        <td>{{ $shop->is_expired}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="row pt-3">
                                {{ $usershops->links() }}
                            </div>
                        </div>
                    </div>         
                </div>
            </div>
        </div>
    </div>
@endsection