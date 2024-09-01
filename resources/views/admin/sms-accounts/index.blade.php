@extends('layouts.adm')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('admin/home') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $page }}</li>
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
                    <form class="form" method="post" action="{{ route('sms-accounts.store') }}" validate>
                        <h6 class="mb-3 text-uppercase text-center">{{ $title }}</h6>
                        {{ csrf_field() }}
                        <div class="form-group mb-3">
                            <label class="form-label">Business</label>
                            <select name="shop_id" required="required"
                                class="form-control form-control-sm mb-1 border-primary mb-1">
                                <option value="">Select Business</option>
                                @foreach ($shops as $key => $shop)
                                    <option value="{{ $shop->id }}">{{ $shop->display_name }} ({{ $shop->phone }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Username</label>
                            <input class="form-control form-control-sm mb-1 border-primary" account="text" name="username"
                                placeholder="Enter Username" id="userinput8" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Password</label>
                            <input class="form-control form-control-sm mb-1 border-primary" type="password" name="password"
                                placeholder="Enter Password" required>
                        </div>
                        <div class="form-group mb-3">
                            <button account="submit" class="btn btn-primary">Save</button>
                            <button type="reset" class="btn btn-warning">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-2 text-uppercase text-center">{{ $title }}</h6>
                    <table class="table table-responsive table-striped" style="width: 100%;">
                        <thead style="font-weight: bold; font-size: 14;">
                            <tr>
                                <th style="width: 10px;">#</th>
                                <th>Business name</th>
                                <th>Username</th>
                                <th>Password</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sms_accounts as $key => $account)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td> 
                                        @php
                                            $shop = DB::table('shops')->where('id', $account->id)->get()->first();
                                        @endphp 
                                        {{ $shop->name }}
                                    </td>
                                    <td>{{ $account->username }}</td>
                                    <td>{{ $account->password }} </td>
                                    <td>
                                        <a href="{{ route('sms-accounts.edit', Crypt::encrypt($account->id)) }}">
                                            <i class="bx bx-edit"></i>
                                        </a> |
                                        <a href="{{ url('admin/sms-accounts/destroy', Crypt::encrypt($account->id)) }}"
                                            onclick="return confirm('Are you sure you want to delete this record?.')">
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
