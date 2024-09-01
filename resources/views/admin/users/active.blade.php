@extends('layouts.adm')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('admin/home')}}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                    <li class="breadcrumb-item active" aria-current="page">Active Users</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">

        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-responsive table-striped display nowrap" style="width: 100%;">
                            <thead style="font-weight: bold; font-size: 14;">
                                <tr>
                                    <th style="width: 10px;">#</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Phone number</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- @foreach($users as $key =>  $user)
                                <tr>
                                    <td>{{ $key+1  }}</td>
                                    <td>{{ $user['first_name'] }}</td>
                                    <td>{{ $user['last_name'] }}</td>
                                    <td>{{ $user['phone'] }} </td>
                                    @if($user['away'])
                                    <td><a href="#"><i class="fa fa-circle text-orange"></i></a></td>
                                    @else
                                    <td><a href="#"><i class="fa fa-circle text-success"></i></a></td>
                                    @endif
                            
                                </tr>
                                @endforeach --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection