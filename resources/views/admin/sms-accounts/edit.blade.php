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
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form class="form" method="post"
                        action="{{ route('sms-accounts.update', Crypt::encrypt($smsacc->id)) }}" validate>
                        @csrf
                        @method('PUT')
                        <h6 class="mb-3 text-uppercase text-center">{{ $title }}</h6>
                        {{ csrf_field() }}
                        <input type="hidden" name="id" value="{{ Crypt::encrypt($smsacc->id) }}">
                        <div class="form-group mb-3">
                            <label class="form-label">Select Business</label>
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
                                placeholder="Enter Username" value="{{ $smsacc->username }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">Password</label>
                            <input class="form-control form-control-sm mb-1 border-primary" type="password" name="password"
                                placeholder="Enter Password" required>
                        </div>
                        <div class="form-group mb-3">
                            <button account="submit" class="btn btn-primary">Edit Account</button>
                            <button type="reset" class="btn btn-warning">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
