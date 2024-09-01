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
        <div class="ms-auto">

        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3 mt-2 text-uppercase text-center">New Subscription Type</h6>
                    <form class="form row g-3" method="post" action="{{ route('subscriptions.store') }}" validate>
                        {{csrf_field()}}
                        <div class="col-md-12">
                            <label class="form-label">Subscription Title</label>
                            <input class="form-control form-control-sm mb-1 border-primary" subs="text" name="title" placeholder="Enter Subscription Title" id="userinput8" required>
                        </div>
                        <div class="col-md-12">
                            <label for="userinput8">Description</label>
                            <textarea name="description" class="form-control form-control-sm mb-1 border-primary" placeholder="Please Enter subs- description" required></textarea>
                        </div>
                        <div class="col-md-12">
                            <button subs="submit" class="btn btn-primary btn-sm">Save</button>
                            <button type="reset" class="btn btn-warning btn-sm">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h6 class="my-3 text-uppercase text-center">{{$title}}</h6>
                    <table class="table table-responsive table-striped" style="width: 100%;">
                        <thead style="font-weight: bold; font-size: 14;">
                            <tr>
                                <th style="width: 10px;">#</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subscriptions as $key => $subs)
                            <tr>
                                <td>{{ $key+1  }}</td>
                                <td>{{ $subs->title }}</td>
                                <td>{{ $subs->description }} </td>
                                <td>
                                    <a  href="{{  route('subscriptions.edit', Crypt::encrypt($subs->id)) }}">
                                        <i class="bx bx-edit"></i>
                                    </a> |
                                    <a href="{{ url('admin/subscriptions/destroy', Crypt::encrypt($subs->id)) }}" onclick="return confirm('Are you sure you want to delete this record?.')">
                                        <i class="bx bx-trash text-danger"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
