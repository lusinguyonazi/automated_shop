@extends('layouts.app')

@section('content')

    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                    <li class="breadcrumb-item text-uppercase">
                        @if (app()->getLocale() == 'en')
                            @if ($page == 'Home' || $page == 'Point of Sale')
                                <small>{{ trans('navmenu.expire_notify') }}@if ($status > 7)
                                        <b style="color: green;">{{ date('d M, Y H:m:s:A', strtotime($payment->expire_date)) }}
                                            ({{ $status }} {{ trans('navmenu.days') }})</b>
                                    @else
                                        <b style="color: red;">{{ date('d M, Y H:m:s:A', strtotime($payment->expire_date)) }}
                                            ({{ $status }} {{ trans('navmenu.days') }})</b>
                                    @endif </small>
                            @endif
                        @else
                            @if ($page == 'Home' || $page == 'Point of Sale')
                                <small>{{ trans('navmenu.expire_notify') }} @if ($status > 7)
                                        <b style="color: green;">{{ date('d M, Y H:m:s:A', strtotime($payment->expire_date)) }}
                                        ({{ trans('navmenu.days') }} {{ $status }})</b> @else<b
                                            style="color: red;">{{ date('d M, Y H:m:s:A', strtotime($payment->expire_date)) }}
                                            ({{ trans('navmenu.days') }} {{ $status }})</b>
                                    @endif </small>
                            @endif
                        @endif
                    </li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">

        </div>
    </div>
    <!--end breadcrumb-->
    <form class="row g-3 d-flex justify-content-end dashform" action="{{ url('home') }}" method="POST">
        @csrf
        <div class="col-md-5 pt-2"></div>
        <input type="hidden" name="start_date" id="start_input" value="{{ $start_date }}">
        <input type="hidden" name="end_date" id="end_input" value="{{ $end_date }}">
        <!-- Date and time range -->
        <div class="form-group col-md-4">
            <div class="input-group d-flex justify-content-end">
                <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange">
                    <span><i class="fa fa-calendar"></i></span>
                    <i class="fa fa-caret-down"></i>
                </button>
            </div>
        </div>
        <div class="form-group col-md-3">
            <select name="store" class="form-select form-select-md" onchange='this.form.submit();'>
                @if (!is_null($currstore))
                    <option value="{{ $currstore->id }}">{{ $currstore->name }}</option>
                @endif
                <option value="">All Stores</option>
                @foreach ($shops as $store)
                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                @endforeach
            </select>
        </div>
    </form>
    <hr>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
        <div class="col">
            <div class="card radius-10 ">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <p class="mb-0 fs-6">{{ trans('navmenu.total_sales') }}</p>
                        <p class="mb-0 p-0 ms-auto">
                            <span>
                                <i class='bx bx-line-chart fs-3 text-primary'></i>
                            </span>
                        </p>
                    </div>
                    <div class="progress mb-2" style="height:4px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 55%" aria-valuenow="75"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex fs-6 align-items-center">
                        <h5 class="mb-0 py-1 text-primary">{{ $currency }}
                            {{-- {{ number_format($total_sales, 2, '.', ',') }} --}}
                            {{ App\Http\Controllers\Web\HomeController::number_format_short($total_sales) }}
                        </h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 ">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <p class="mb-0 fs-6">{{ trans('navmenu.total_collections') }}</p>
                        <p class="mb-0 p-0 ms-auto">
                            <span>
                                <i class='bx bx-money fs-3 text-success'></i>
                            </span>
                        </p>
                    </div>
                    <div class="progress mb-2" style="height:4px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 55%" aria-valuenow="75"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex fs-6 align-items-center">
                        <h5 class="mb-0 py-1 text-success">{{ $currency }}
                            {{-- {{ number_format($total_collections, 2, '.', ',') }} --}}
                            {{ App\Http\Controllers\Web\HomeController::number_format_short($total_collections) }}
                        </h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 ">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <p class="mb-0 fs-6">{{ trans('navmenu.total_debts') }}</p>
                        <p class="mb-0 p-0 ms-auto">
                            <span>
                                <i class='bx bx-credit-card-front fs-3 text-danger'></i>
                            </span>
                        </p>
                    </div>
                    <div class="progress mb-2" style="height:4px;">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 55%" aria-valuenow="75"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex fs-6 align-items-center">
                        <h5 class="mb-0 py-1 text-danger">{{ $currency }}
                            {{ App\Http\Controllers\Web\HomeController::number_format_short($total_debts) }}
                        </h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 ">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <p class="mb-0 fs-6">{{ trans('navmenu.total_expenses') }}</p>
                        <p class="mb-0 p-0 ms-auto">
                            <span>
                                <i class='bx bx-line-chart-down fs-3 text-warning'></i>
                            </span>
                        </p>
                    </div>
                    <div class="progress mb-2" style="height:4px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 55%" aria-valuenow="75"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex fs-6 align-items-center">
                        <h5 class="mb-0 py-1 text-warning">{{ $currency }}
                            {{ App\Http\Controllers\Web\HomeController::number_format_short($total_expenses) }}
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->

    <div class="row">
        <div class="col-12 col-lg-6 col-xl-6 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div id="chart12"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6 col-xl-6 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div id="chart13"></div>
                </div>
            </div>
        </div>
    </div><!--End Row-->
    <div class="card radius-10">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div>
                    <h5 class="mb-0">{{trans('navmenu.top_selling')}}</h5>
                </div>
                <div class="font-22 ms-auto"><i class="bx bx-dots-horizontal-rounded"></i>
                </div>
            </div>
            <hr>
            <ul class="nav nav-tabs nav-success" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" data-bs-toggle="tab" href="#products" role="tab" aria-selected="true">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class='bx bx-shopping-bag font-18 me-1'></i>
                            </div>
                            <div class="tab-title">{{trans('navmenu.products')}}</div>
                        </div>
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" data-bs-toggle="tab" href="#services" role="tab" aria-selected="false">
                        <div class="d-flex align-items-center">
                            <div class="tab-icon"><i class='bx bx-cog font-18 me-1'></i>
                            </div>
                            <div class="tab-title">{{trans('navmenu.services')}}</div>
                        </div>
                    </a>
                </li>
            </ul>
            <div class="tab-content py-3">
                <div class="tab-pane fade show active" id="products" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{trans('navmenu.product')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.price')}}</th>
                                    <th style="text-align: right;">{{trans('navmenu.amount')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $key => $product)
                                <tr>
                                    <td><strong>{{$product->name}}</strong></td>
                                    <td style="text-align: center;">{{$product->quantity}}</td>
                                    <td style="text-align: center;">{{number_format($product->unitprice-$product->unitdiscount, 2, '.', ',')}}</td>
                                    <td style="text-align: right;"><strong>{{$currency}} {{number_format($product->price-$product->discount, 2, '.', ',')}}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="services" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>{{trans('navmenu.service')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.price')}}</th>
                                    <th style="text-align: right;">{{trans('navmenu.amount')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($services as $key => $service)
                                <tr>
                                    <td><strong>{{$service->name}}</strong></td>
                                    <td style="text-align: center;">{{$service->quantity}}</td>
                                    <td style="text-align: center;">{{number_format($service->unitprice-$service->unitdiscount)}}</td>
                                    <td style="text-align: center;"><strong>{{$currency}} {{number_format($service->total-$service->discount, 2, '.', ',')}}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('page-scripts')

    <!-- highcharts js -->
    <script src="{{ asset('assets/plugins/highcharts/js/highcharts.js') }}"></script>
    <script src="{{ asset('assets/plugins/highcharts/js/highcharts-more.js') }}"></script>
    <script src="{{ asset('assets/plugins/highcharts/js/variable-pie.js') }}"></script>
    <script src="{{ asset('assets/plugins/highcharts/js/solid-gauge.js') }}"></script>
    <script src="{{ asset('assets/plugins/highcharts/js/highcharts-3d.js') }}"></script>
    <script src="{{ asset('assets/plugins/highcharts/js/cylinder.js') }}"></script>
    <script src="{{ asset('assets/plugins/highcharts/js/funnel3d.js') }}"></script>
    <script src="{{ asset('assets/plugins/highcharts/js/exporting.js') }}"></script>
    <script src="{{ asset('assets/plugins/highcharts/js/export-data.js') }}"></script>
    <script src="{{ asset('assets/plugins/highcharts/js/accessibility.js') }}"></script>
    <script>
        $(function () {
            "use strict";

            var labels = <?php echo json_encode($labels); ?>;
            var sales = <?php echo json_encode($salesdata); ?>;
            var totalsales = <?php echo $total_sales; ?>;
            var grossprofit = <?php echo $gross_profit; ?>;
            var totalexpenses = <?php echo $total_expenses; ?>;
            var netprofit = <?php echo $gross_profit-$total_expenses; ?>;

            var gelabels = <?php echo json_encode($gelabels); ?>;
            var grosses = <?php echo json_encode($grosses); ?>;
            var expenses = <?php echo json_encode($expensesdata); ?>;
            // chart 12
            Highcharts.chart('chart12', {
                chart: {
                    styledMode: true
                },
                credits: {
                    enabled: false
                },
                title: {
                    text: "{{trans('navmenu.sales_per_day')}}"
                },
                xAxis: {
                    categories: labels
                },
                labels: {
                    items: [{
                        html: 'Total Profit Summary',
                        style: {
                            left: '50px',
                            top: '18px',
                            color: ( // theme
                                Highcharts.defaultOptions.title.style && Highcharts.defaultOptions.title.style.color) || 'black'
                        }
                    }]
                },
                series: [{
                    type: 'spline',
                    name: 'Sales',
                    data: sales,
                    marker: {
                        lineWidth: 2,
                        lineColor: Highcharts.getOptions().colors[3],
                        fillColor: 'white'
                    }
                }, {
                    type: 'pie',
                    name: 'Amount',
                    data: [{
                        name: 'Sales',
                        y: totalsales,
                        color: Highcharts.getOptions().colors[0] // Saless color
                    }, {
                        name: 'Gross Profit',
                        y: grossprofit,
                        color: Highcharts.getOptions().colors[2] // Gross profits color
                    }, {
                        name: 'Expenses',
                        y: totalexpenses,
                        color: Highcharts.getOptions().colors[1] // Expenses color
                    }, {
                        name: 'Net Profit',
                        y: netprofit,
                        color: Highcharts.getOptions().colors[3] // Net Profits color
                    }],
                    center: [100, 80],
                    size: 100,
                    showInLegend: false,
                    dataLabels: {
                        enabled: false
                    }
                }]
            });

            // chart 13
            Highcharts.chart('chart13', {
                chart: {
                    zoomType: 'xy',
                    styledMode: true
                },
                credits: {
                    enabled: false
                },
                title: {
                    text: 'Average Gross Profit and Expenses'
                },
                subtitle: {
                    text: 'Business Evaluation'
                },
                xAxis: [{
                    categories: gelabels,
                    crosshair: true
                }],
                yAxis: [{ // Primary yAxis
                    labels: {
                        format: '',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    title: {
                        text: 'Expenses',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    }
                }, { // Secondary yAxis
                    title: {
                        text: 'Gross Profit',
                        style: {
                            color: Highcharts.getOptions().colors[0]
                        }
                    },
                    labels: {
                        format: '',
                        style: {
                            color: Highcharts.getOptions().colors[0]
                        }
                    },
                    opposite: true
                }],
                tooltip: {
                    shared: true
                },
                legend: {
                    layout: 'vertical',
                    align: 'left',
                    x: 120,
                    verticalAlign: 'top',
                    y: 100,
                    floating: true,
                    backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || // theme
                    'rgba(255,255,255,0.25)'
                },
                series: [{
                    name: "{{trans('navmenu.gross_profit')}}",
                    type: 'column',
                    yAxis: 1,
                    data: grosses,
                }, {
                    name: "{{trans('navmenu.expenses')}}",
                    type: 'spline',
                    data: expenses
                }]
            });
        });
    </script>
@endsection