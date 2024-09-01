@extends('layouts.app')
@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('/home')}}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-md-11 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
            <hr/>
            <form class="row g-3 dashform" action="{{url('consolidated')}}" method="POST">
                @csrf
                <div class="col-md-3"></div>
                <div class="col-md-3">
                    <input type="text" name="sale_date" id="saledate" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-3" autocomplete="off">
                </div>
                <input type="hidden" name="start_date" id="start_input" value="">
                <input type="hidden" name="end_date" id="end_input" value="">
                <!-- Date and time range -->
                <div class=" col-md-6">
                    <div class="input-group">
                        <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange">
                            <span><i class="bx bx-calendar"></i></span>
                            <i class="bx bx-caret-down"></i>
                        </button>
                    </div>
                </div>
            </form>
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-success" role="tablist">
                        
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#report-excel" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-export font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">{{trans('navmenu.consolidated_report')}} (Excel)</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#report-chart" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-export font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">{{trans('navmenu.consolidated_report')}} Chart</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="report-excel" role="tabpanel">
                            <div class="col-xs-12" style="text-align: center; text-transform: uppercase; color: blue; border-bottom: 2px solid #82B1FF; margin-bottom: 8px;">
                                @if(!is_null($shop->logo_location))
                                <figure>
                                    <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                                </figure>
                                @endif
                                <!-- <h5>All </h5> -->
                                <h6>
                                    {{trans('navmenu.consolidated_report')}} <br><br> 
                                    <b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b>
                                </h6>
                            </div>
                            <div class="col-xs-12 table-responsive">
                                <table id="consolidated" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                    <thead style="background:#BDBDBD;">
                                        <tr>
                                            <th>{{trans('navmenu.business_name')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.sales')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.cost_of_sales')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.gross_profit')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.operating_expense')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.profit')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                            $bizs = array();
                                            $sales = array();
                                            $cosales = array();
                                            $gps = array();
                                            $oexps = array();
                                            $profits = array();
                                        ?>
                                        @foreach($totals as $index => $total)
                                        <?php 
                                            array_push($bizs, $total['bizname']);
                                            array_push($sales, ($total['price']-$total['discount']));
                                            array_push($cosales, $total['buying_price']+0);
                                            array_push($gps, ($total['price']-$total['discount'])-$total['buying_price']);
                                            array_push($oexps, $total['amount']);
                                            array_push($profits, (($total['price']-$total['discount'])-$total['buying_price'])-$total['amount']);
                                        ?>
                                        <tr>
                                            <td>{{$total['bizname']}}</td>
                                            <td style="text-align: center;">{{number_format(($total['price']-$total['discount']))}}</td>
                                            <td style="text-align: center;">{{number_format($total['buying_price'])}}</td>
                                            <td style="text-align: center;">{{number_format(($total['price']-$total['discount'])-$total['buying_price'])}}</td>
                                            <td style="text-align: center;">{{number_format($total['amount'])}}</td>
                                            <td style="text-align: center;">{{number_format((($total['price']-$total['discount'])-$total['buying_price'])-$total['amount'])}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th><b>{{trans('navmenu.total')}}</b></th>
                                            <th style="text-align: center;"><b>{{number_format($tsales)}}</b></th>
                                            <th style="text-align: center;"><b>{{number_format($tcsales)}}</b></th>
                                            <th style="text-align: center;"><b>{{number_format($tsales-$tcsales)}}</b></th>
                                            <th style="text-align: center;"><b>{{number_format($texpenses)}}</b></th>
                                            <th style="text-align: center;"><b>{{number_format(($tsales-$tcsales)-$texpenses)}}</b></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>      
                        </div>
                        <div class="tab-pane fade" id="report-chart" role="tabpanel">
                            <div class="card">
                                <div class="card-body">
                                    <div id="chart7"></div>
                                </div>
                            </div>
                        </div>
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

            var bizs = <?php echo json_encode($bizs); ?>;
            var sales = <?php echo json_encode($sales); ?>;
            var cosales = <?php echo json_encode($cosales); ?>;
            var gps = <?php echo json_encode($gps); ?>;
            var oexps = <?php echo json_encode($oexps); ?>;
            var profits = <?php echo json_encode($profits); ?>;
            // chart7
            Highcharts.chart('chart7', {
                chart: {
                    type: 'bar',
                    styledMode: true
                },
                credits: {
                    enabled: false
                },
                title: {
                    text: "{{trans('navmenu.consolidated_report')}}"
                },
                subtitle: {
                    text: ''
                },
                xAxis: {
                    categories: bizs,
                    title: {
                        text: null
                    }
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: "{{trans('navmenu.amount')}}",
                        align: 'high'
                    },
                    labels: {
                        overflow: 'justify'
                    }
                },
                tooltip: {
                    valueSuffix: ''
                },
                plotOptions: {
                    bar: {
                        dataLabels: {
                            enabled: true
                        }
                    }
                },
                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'top',
                    x: -40,
                    y: 80,
                    floating: true,
                    borderWidth: 1,
                    backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF',
                    shadow: true
                },
                credits: {
                    enabled: false
                },
                series: [{
                    name: "{{trans('navmenu.profit')}}",
                    data: profits
                }, {
                    name: "{{trans('navmenu.operating_expense')}}",
                    data: oexps
                }, {
                    name: "{{trans('navmenu.gross_profit')}}",
                    data: gps
                }, {
                    name: "{{trans('navmenu.cost_of_sales')}}",
                    data: cosales
                }, {
                    name: "{{trans('navmenu.sales')}}",
                    data: sales
                }]
            });
            
        });
    </script>
@endsection
    <link rel="stylesheet" href="{{asset('css/DatePickerX.css')}}">
    <script src="{{asset('js/DatePickerX.min.js')}}"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="sale_date"]');
            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });
        });
    </script>