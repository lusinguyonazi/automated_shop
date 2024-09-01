@extends('layouts.adm')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('admin/home')}}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                    <li class="breadcrumb-item active" aria-current="page"> 
                        {{$title}} From <span class="text-primary" >{{ $duration['from'] }}</span> To <span class="text-primary">{{$duration['to'] }} </span>
                    </li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-md-12 mx-auto">
            <form class="dashform row" action="{{ url('admin/activated-payments') }}" method="get">
                @csrf
                <div class="col-md-3 d-flex justify-content-start"></div>
                <input type="hidden" name="start_date" id="start_input" value="">
                <input type="hidden" name="end_date" id="end_input" value="">
                <!-- Date and time range -->
                <div class="col-md-9">
                    <div class="input-group d-flex justify-content-end mb-2">
                        <a class="btn btn-primary mx-1" style="border-radius: 5px;" 
                            href="{{ url('admin/activated-payments/' . $duration['from'] . '/' . $duration['to'] . '/' . 'pdf') }}">PDF</a>
                        <a class="btn btn-primary mx-1" style="border-radius: 5px;"
                            href="{{ url('admin/activated-payments/' . $duration['from'] . '/' . $duration['to'] . '/' . 'excel') }}">EXCEL</a>
                        <a class="btn btn-primary mx-1" style="border-radius: 5px;"
                            href="{{ url('admin/activated-payments/' . $duration['from'] . '/' . $duration['to'] . '/' . 'csv') }}">CSV</a>

                        <input id="search" class="typeahead form-control form-control mx-3" type="text"
                            style="width: 45px;" name="username" placeholder="Enter Username" id="search">
                        <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange"><span><i
                                    class="bx bx-calendar"></i></span><i class="fa fa-caret-down"></i></button>
                    </div>
                </div>
            </form>
            
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-success" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" href="{{ url('/admin/payments') }}" role="tab" aria-selected="true">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-list-plus font-18 me-1'></i></div>
                                    <div class="tab-title">All Payment Transactions</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" href="{{ url('/admin/activated-payments') }}" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-list-check font-18 me-1'></i></div>
                                    <div class="tab-title">All Activated Payments</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" href="{{ url('/admin/agent-activations') }}" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-list-minus font-18 me-1'></i></div>
                                    <div class="tab-title">Activations By Agent</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" href="{{ url('/admin/activations-once') }}" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-list-ul font-18 me-1'></i></div>
                                    <div class="tab-title">Activated At Least Once</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="tab_1-1" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-striped display nowrap" style="width: 100%;">
                                    <thead style="font-weight: bold; font-size: 14;">
                                        <tr>
                                            <th style="width: 10px;">#</th>
                                            <th>Business</th>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Mobile</th>
                                            <th>Pay number</th>
                                            <th>TXN ID</th>
                                            <th>Code</th>
                                            <th>Amount paid</th>
                                            <th>Period</th>
                                            <th>Created At</th>
                                            <th>Expire date</th>
                                            <th>Is expired?</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($users->chunk(200) as $uchunk)
                                        @foreach($uchunk as $key => $payment)
                                        <tr>
                                            <td>{{ $key+1  }}</td>
                                            <td>{{ $payment->name}} </td>
                                            <td>{{ $payment->first_name}}</td>
                                            <td>{{ $payment->last_name}} </td>
                                            <td>{{ $payment->phone }} </td>
                                            <td>{{ $payment->phone_number }}</td>
                                            <td>{{ $payment->transaction_id }} </td>
                                            <td>{{ $payment->code}}</td>
                                            <td>{{ $payment->amount_paid }} </td>
                                            <td>{{ $payment->period }} </td>
                                            <td>{{ $payment->created_at}} </td>
                                            <td>{{ $payment->expire_date }} </td>
                                            <td>{{ $payment->is_expired }} </td>
                                            
                                        </tr>
                                        @endforeach
                                    @endforeach
                                    </tbody>
                                </table>
                                <div class="mt-3">
                                    {{ $users->links() }}
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