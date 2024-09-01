@extends('layouts.adm')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('admin/home')}}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <h6 class="mb-3 mt-2 text-uppercase text-center">{{$title}}  {{$duration}}</h6>
                        <table class="table table-responsive table-striped display nowrap" style="width: 100%;">
                            <thead style="font-weight: bold; font-size: 14;">
                                <tr>
                                    <th style="width: 10px;">#</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Mobile</th>
                                    <th>Business Name</th>
                                    <th>Date registered</th>
                                    <th>Is Default?</th>
                                    <th>Expire Date</th>
                                    <!-- <th>Is Expired?</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shops as $key => $shop)
                                <tr>
                                    <td>{{ $key+1  }}</td>
                                    <td>{{ $shop->first_name }}</td> 
                                    <td>{{$shop->last_name}} </td>
                                    <td>{{ $shop->phone }}</td>
                                    <td>{{ $shop->display_name }} </td>
                                    <td>{{ $shop->created_at}} </td>
                                    <td>{{ $shop->is_default}}</td>
                                    <td>{{ $shop->expire_date }}</td>
                                    <!-- <td>{{ $shop->is_expired}}</td> -->
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection