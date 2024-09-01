@extends('layouts.adm')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('admin/home') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $page }}</li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $title }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-md-12 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <H5 class="mb-md-0 p-2 text-uppercase">EDIT USER DETAILS</H5>
                        <form class="form" method="post" id="new-user" action="{{ route('users.update', Crypt::encrypt($user->id)) }}" validate>
                            @csrf
                            @method('PUT')
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-4 form-group mb-3">
                                        <label class="control-label py-2">First Name<span
                                                style="margin-left: 5px; color: red;">
                                                *</span></label>
                                        <input value="{{ $user->first_name }}" class="form-control form-control-sm border-primary" type="text" name="first_name"
                                            placeholder="Enter First Name" id="userinput8" required>
                                    </div>
                                    <div class="col-md-4 form-group mb-3">
                                        <label class="control-label py-2">Last Name<span
                                                style="margin-left: 5px; color: red;">
                                                *</span></label>
                                        <input value="{{ $user->last_name }}" class="form-control form-control-sm border-primary" type="text" name="last_name"
                                            placeholder="Enter Last Name" id="userinput8" required>
                                    </div>
                                    <div class="col-md-4 form-group mb-3">
                                        <label class="control-label py-2">Phone Number<span
                                                style="margin-left: 5px; color: red;"> *</span></label>
                                        <input value="{{ $user->phone }}" class="form-control form-control-sm border-primary" type="text" name="phone"
                                            placeholder="Enter Phone Number" id="userinput8" required>
                                    </div>
                                    <div class="col-md-4 form-group mb-3">
                                        <label class="control-label py-2">Email Address<span
                                                style="margin-left: 5px; color: red;"> *</span></label>
                                        <input value="{{ $user->email }}" class="form-control form-control-sm border-primary" type="email" name="email"
                                            placeholder="Enter Email address" id="userinput8" required>
                                    </div>
                                    <div class="col-md-4 form-group mb-3">
                                        <label class="control-label py-2">Password<span
                                                style="margin-left: 5px; color: red;">
                                                *</span></label>
                                        <input class="form-control form-control-sm border-primary" type="password" name="password"
                                            placeholder="Enter Password" id="userinput8" required>
                                    </div>
                                    <div class="col-md-4 form-group mb-3">
                                        <label class="control-label py-2">Confirm Password<span
                                                style="margin-left: 5px; color: red;"> *</span></label>
                                        <input class="form-control form-control-sm border-primary" type="password"
                                            name="confirm_password" placeholder="Re-enterPassword" id="userinput8"
                                            required>
                                    </div>
                                    <div class="col-md-4 form-group mb-3">
                                        <label class="control-label py-2">User Role<span
                                                style="margin-left: 5px; color: red;">
                                                *</span></label>
                                        <select name="role" class="form-control form-control-sm">
                                            <option>Choose a Role</option>
                                            @foreach ($roles as $role)
                                                <option>{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-12 form-group mt-2">
                                        <a href="{{ url('admin/users') }}" type="button" class="btn btn-warning mx-2">
                                            <i class="icon-cross2"></i> Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="icon-check2"></i> Save
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
