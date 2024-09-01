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
            <form class="dashform row" action="{{ url('admin/service-payments') }}" method="get">
                @csrf
                <div class="col-md-3 d-flex justify-content-start"></div>
                <input type="hidden" name="start_date" id="start_input" value="">
                <input type="hidden" name="end_date" id="end_input" value="">
                <!-- Date and time range -->
                <div class="col-md-9">
                    <div class="input-group d-flex justify-content-end mb-2">
                        <a class="btn btn-secondary" href="{{ url('admin/payments-export/' . 'pdf') }}">PDF</a>
                        <a class="btn btn-secondary" href="{{ url('admin/payments-export/' . 'excel') }}">EXCEL</a>
                        <a class="btn btn-secondary" href="{{ url('admin/payments-export/' . 'csv') }}">CSV</a>

                        <input id="search" class="typeahead form-control form-control mx-3" type="text"
                            style="width: 45px;" name="username" placeholder="Enter Username" id="search">
                        <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange"><span><i
                                    class="bx bx-calendar"></i></span><i class="fa fa-caret-down"></i></button>
                    </div>
                </div>
            </form>
            <div class="card">
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab_1-4" role="tabpanel">
                            <div class="px-2 pt-3 rounded">
                                <form class="form row needs-validation" method="post"
                                    action="{{ route('payments.store') }}" validate>
                                    {{ csrf_field() }}
                                    <input type="hidden" name="api_key" value="WtUCp2KDdPNzcnCPjHhtJAxYDZl3NVuu">
                                    <div class="col-md-3">
                                        <input class="form-control form-control-sm mb-3" type="text"
                                            placeholder="Enter Sender's Phone number" id="userinput6" name="phone_number"
                                            required>
                                    </div>
                                    <div class="col-md-3">
                                        <input class="form-control form-control-sm mb-3" type="text"
                                            name="transaction_id" id="userinput8" placeholder="Enter Transaction ID"
                                            required>
                                    </div>
                                    <div class="col-md-3">
                                        <input class="form-control form-control-sm mb-3" type="number"
                                            name="amount_paid" id="userinput5" placeholder="Enter amount paid" required>
                                    </div>
                                    <div class="col-md-3">
                                        <a href="{{ url('admin/payments') }}" class="btn btn-warning btn-sm">Cancel</a>
                                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="tab-pane fade show active" id="tab_1-0" role="tabpanel">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection
