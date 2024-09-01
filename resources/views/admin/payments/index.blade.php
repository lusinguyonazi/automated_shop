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
            <form class="dashform row d-flex justify-content-end mb-1" action="{{ url('admin/service-payments') }}"
                method="get">
                @csrf
                <input type="hidden" name="start_date" id="start_input" value="">
                <input type="hidden" name="end_date" id="end_input" value="">
                @php
                    $searchTerm = $searchTerm ?? 'noterm';
                @endphp
                <div class="col-md-3">
                    <a class="btn btn-secondary" style="border-radius: 5px;"
                        href="{{ url('admin/payments-export/' . $duration['from'] . '/' . $duration['to'] . '/' . $searchTerm . '/' . 'pdf') }}">PDF</a>
                    <a class="btn btn-secondary" style="border-radius: 5px;"
                        href="{{ url('admin/payments-export/' . $duration['from'] . '/' . $duration['to'] . '/' . $searchTerm . '/' . 'excel') }}">EXCEL</a>
                    <a class="btn btn-secondary" style="border-radius: 5px;"
                        href="{{ url('admin/payments-export/' . $duration['from'] . '/' . $duration['to'] . '/' . $searchTerm . '/' . 'csv') }}">CSV</a>
                </div>
                <div class="col-md-5">
                    <div class="input-group">
                        <input class="typeahead form-control" type="text" style="width: 45px;" name="search_date"
                            placeholder="Search here . . .">
                        <button class="btn btn-outline-primary" type="submit" id="button-addon2">
                            <i class="bx bx-search"></i>
                        </button>
                    </div>
                </div>
                <!-- Date and time range -->
                <div class="col-md-4 d-flex justify-content-end">
                    <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange"><span><i
                                class="bx bx-calendar"></i></span><i class="fa fa-caret-down"></i></button>
                </div>
            </form>
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-success" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" href="{{ url('/admin/payments') }}" role="tab"
                                aria-selected="true">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-list-plus font-18 me-1'></i></div>
                                    <div class="tab-title">All Payment Transactions</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" href="{{ url('/admin/activated-payments') }}" role="tab"
                                aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-list-check font-18 me-1'></i></div>
                                    <div class="tab-title">All Activated Payments</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" href="{{ url('/admin/agent-activations') }}" role="tab"
                                aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-list-minus font-18 me-1'></i></div>
                                    <div class="tab-title">Activations By Agent</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" href="{{ url('/admin/activations-once') }}" role="tab"
                                aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-list-ul font-18 me-1'></i></div>
                                    <div class="tab-title">Activated At Least Once</div>
                                </div>
                            </a>
                        </li>
                    </ul>
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
                            <div class="table-responsive">
                                {{-- <table id="example7" class="table table-striped display nowrap" style="width: 100%;"> --}}
                                <table style="font-size: 12px;" class="table table-striped display nowrap"
                                    style="width: 100%;">
                                    <thead style="font-weight: bold; font-size: 14;">
                                        <tr>
                                            <th style="width: 10px;">#</th>
                                            <th style="font-size: 12px;">Phone number</th>
                                            <th style="font-size: 12px;">Transaction ID</th>
                                            <th style="font-size: 12px;">Code</th>
                                            <th style="font-size: 12px;">Amount paid</th>
                                            <th style="font-size: 12px;">Period</th>
                                            <th style="font-size: 12px;">Created At</th>
                                            <th style="font-size: 12px;">Activated At</th>
                                            <th style="font-size: 12px;">Status</th>
                                            <th style="font-size: 12px;">Expire date</th>
                                            <th style="font-size: 12px;">Is expired?</th>
                                            <th style="font-size: 12px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($payments->chunk(200) as $chunk)
                                            @foreach ($chunk as $key => $payment)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $payment->phone_number }}</td>
                                                    <td>{{ $payment->transaction_id }} </td>
                                                    <td>{{ $payment->code }}</td>
                                                    <td>{{ $payment->amount_paid }} </td>
                                                    <td>{{ $payment->period }} </td>
                                                    <td>{{ $payment->created_at }} </td>
                                                    <td> {{ $payment->activation_time }} </td>
                                                    <td> {{ $payment->status }} </td>
                                                    <td>{{ $payment->expire_date }} </td>
                                                    <td>{{ $payment->is_expired }} </td>
                                                    <td>
                                                        <a href="{{ route('payments.edit', $payment->id) }}">
                                                            <i class="fa fa-edit"></i>
                                                        </a> |
                                                        <a href="{{ url('admin/payments/destroy', ['id' => $payment->id]) }}"
                                                            onclick="return confirm('Are you sure you want to delete this record')">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="mt-3">
                                    {{ $payments->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection
