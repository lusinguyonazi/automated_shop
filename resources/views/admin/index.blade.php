@extends('layouts.adm')

@section('content')
    <div class="card shadow-none bg-transparent">
        <div class="card-body py-0">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h4 class="mb-md-0 text-uppercase">Audience Overview</h4>
                </div>
                <div class="col-md-3">
                    <h6 class="mb-md-0">
                        <span class="text-primary">{{ date('d-m-Y', strtotime($start)) }}</span>&nbsp; TO &nbsp;
                        <span class="text-primary">{{ date('d-m-Y', strtotime($end)) }}</span>
                    </h6>
                </div>
                <div class="col-md-5">
                    <form class="dashform row" action="{{ url('admin/home') }}" method="get">
                        @csrf
                        <div class="col-md-3 d-flex justify-content-start"></div>
                        <input type="hidden" name="start_date" id="start_input" value="">
                        <input type="hidden" name="end_date" id="end_input" value="">
                        <!-- Date and time range -->
                        <div class="col-md-9">
                            <div class="input-group d-flex justify-content-end">
                                <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange"><span><i
                                            class="bx bx-calendar"></i></span><i class="fa fa-caret-down"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
        <div class="col">
            <div class="card radius-10 ">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0 text-primary">{{ $total_users }}</h5>
                        <div class="ms-auto">
                            <i class='bx bx-group fs-3 text-primary'></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <p class="mb-0 fs-6 text-uppercase">REGISTERED USERS</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0 text-success">{{ $total_active }}</h5>
                        <div class="ms-auto">
                            <i class='bx bx-group fs-3 text-success'></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <p class="mb-0 fs-6 text-uppercase">ACTIVE ONLINE USERS</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0 text-danger">{{ $total_shops }}</h5>
                        <div class="ms-auto">
                            <i class='bx bx-shopping-bag fs-3 text-danger'></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <p class="mb-0 fs-6 text-uppercase">REGISTERED SHOPS</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0 text-warning">{{ $total_active_shops }}</h5>
                        <div class="ms-auto">
                            <i class='bx bx-shopping-bag fs-3 text-warning'></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <p class="mb-0 fs-6 text-uppercase">ACTIVE SHOPS</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->

    @php
        // dd($premium_data)
    @endphp

    <div class="row">
        <div class="col-12 col-lg-12 col-xl-12 d-flex">
            <div class="card radius-10 w-100">
                <div class="card-body">
                    <div id="chart12"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-12 col-xl-12 d-flex">
            <div class="card radius-10 overflow-hidden w-100">
                <div class="table-responsive my-2 mx-2">
                    <h4 class="mb-md-0 p-2 text-uppercase">Monthly Total</h4>
                    <table class="table table-striped">
                        <thead style="font-weight: bold; font-size: 14;">
                            <th>MONTH</th>
                            <th style="text-align: center;">REGISTERED</th>
                            <th style="text-align: center;">ACTIVATIONS</th>
                            <th style="text-align: center;">AMOUNT</th>
                        </thead>
                        <tbody>
                            @foreach ($totals as $key => $total)
                                @if ($key < 6)
                                    <tr>
                                        <td> {{ $total['date'] }}</td>
                                        <td style="text-align: center;"> {{ $total['total_reg'] }} </td>
                                        <td style="text-align: center;"> {{ $total['total_act'] }} </td>
                                        <td style="text-align: center;"> {{ number_format($total['amount']) }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!--End Row-->

    {{-- <div class="row">
        <div class="col-12 col-lg-12 col-xl-12 d-flex">
            <div class="card radius-10 overflow-hidden w-100">
                <div class="table-responsive my-2 mx-2">
                    <h4 class="mb-md-0 p-2 text-uppercase">Monthly New Activations</h4>
                    <table class="table">
                        <thead style="font-weight: bold; font-size: 14;">
                            <th>MONTH</th>
                            <th style="text-align: center;">MONTHLY</th>
                            <th style="text-align: center;">QUARTERLY</th>
                            <th style="text-align: center;">SEMI ANNUALLY</th>
                            <th style="text-align: center;">ANNUALLY</th>
                            <th style="text-align: center;">UNCATEGORIZED</th>
                            <th style="text-align: center;">TOTAL</th>
                        </thead>
                        <tbody>
                            @foreach ($new_activations as $key => $total)
                                @if ($key < 6)
                                    <tr>
                                        <td> {{ $total['date'] }} </td>
                                        <td style="text-align: center;"> {{ $total['monthly'] }} </td>
                                        <td style="text-align: center;"> {{ $total['quarterly'] }} </td>
                                        <td style="text-align: center;"> {{ $total['semi_annually'] }} </td>
                                        <td style="text-align: center;"> {{ $total['annually'] }} </td>
                                        <td style="text-align: center;"> {{ $total['uncategorized'] }} </td>
                                        <td style="text-align: center;">
                                            {{ $total['monthly'] + $total['quarterly'] + $total['semi_annually'] + $total['annually'] + $total['uncategorized'] }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                    <div class="text-center">
                        <a href="{{ url('admin/new-activations') }}" class="small-box-footer">More info <i
                                class="fa fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    <!--End Row-->
@endsection
