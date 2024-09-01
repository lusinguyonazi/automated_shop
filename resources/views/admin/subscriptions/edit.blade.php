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
        <div class="col-md-6 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3 mt-2 text-uppercase text-center">EDit Subscription Type Details</h6>
                    <form class="form row g-3" method="post" action="{{ route('subscriptions.update', Crypt::encrypt($subscr_type->id)) }}" validate>
                        @csrf
                        @method('PUT')
                        <div class="col-md-12">
                            <label class="form-label">Subscription Title</label>
                            <input value="{{ $subscr_type->title }}" class="form-control form-control-sm mb-1 border-primary" subs="text" name="title" placeholder="Enter Subscription Title" id="userinput8" required>
                        </div>
                        <div class="col-md-12">
                            <label for="userinput8">Description</label>
                            <textarea name="description" class="form-control form-control-sm mb-1 border-primary" placeholder="Please Enter subs- description" required>
                                {{ $subscr_type->description }}
                            </textarea>
                        </div>
                        <div class="col-md-12">
                            <button subs="submit" class="btn btn-primary btn-sm">Save</button>
                            <button type="reset" class="btn btn-warning btn-sm">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
