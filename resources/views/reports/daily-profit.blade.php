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
            <form class="row g-3 dashform" action="{{url('total-report')}}" method="POST">
                @csrf
                <div class="col-md-4">
                  <a href="{{url('consolidated')}}" class="btn btn-info btn-sm">{{trans('navmenu.consolidated_report')}}</a>
                </div>
                <div class="col-md-3">
                    <input type="text" name="sale_date" id="saledate" placeholder="{{trans('navmenu.pick_date')}}" class="form-control form-control-sm mb-3" autocomplete="off">
                </div>
                <input type="hidden" name="start_date" id="start_input" value="">
                <input type="hidden" name="end_date" id="end_input" value="">
                <!-- Date and time range -->
                <div class=" col-md-5">
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
                                    <div class="tab-title">{{trans('navmenu.daily_profit_loss_report')}} (Excel)</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#report-chart" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-export font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">{{trans('navmenu.daily_profit_loss_report')}} Chart</div>
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
                                <h5>{{$shop->name}}</h5>
                                <h6>
                                    {{trans('navmenu.daily_profit_loss_report')}} <br><br> 
                                    <b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b>
                                </h6>
                            </div>
                            <div class="col-xs-12 table-responsive">
                                <table id="totals" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                    <thead style="background:#BDBDBD;">
                                        <tr>
                                            <th>{{trans('navmenu.date')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.sales')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.cost_of_sales')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.gross_profit')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.operating_expense')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.profit')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($totals as $index => $total)
                                        <tr>
                                            <td>{{$total['date']}}</td>
                                            <td style="text-align: center;">{{number_format(($total['price']-$total['discount'])-$total['output_vat'], 2, '.', ',')}}</td>
                                            <td style="text-align: center;">{{number_format($total['buying_price']-$total['input_vat'], 2, '.', ',')}}</td>
                                            <td style="text-align: center;">{{number_format((($total['price']-$total['discount'])-$total['output_vat'])-($total['buying_price']-$total['input_vat']), 2, '.', ',')}}</td>
                                            <td style="text-align: center;">{{number_format($total['amount'], 2, '.', ',')}}</td>
                                            <td style="text-align: center;">{{number_format(((($total['price']-$total['discount'])-$total['output_vat'])-($total['buying_price']-$total['input_vat']))-$total['amount'], 2, '.', ',')}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th><b>{{trans('navmenu.total')}}</th>
                                            <th style="text-align: center;"><b>{{number_format($tsales, 2, '.', ',')}}</b></th>
                                            <th style="text-align: center;"><b>{{number_format($tcsales, 2, '.', ',')}}</b></th>
                                            <th style="text-align: center;"><b>{{number_format($tsales-$tcsales, 2, '.', ',')}}</b></th>
                                            <th style="text-align: center;"><b>{{number_format($texpenses, 2, '.', ',')}}</b></th>
                                            <th style="text-align: center;"><b>{{number_format(($tsales-$tcsales)-$texpenses, 2, '.', ',')}}</b></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- /.col -->
                        </div>
                        <div class="tab-pane fade" id="report-chart" role="tabpanel">
                            <div class="card">
                                <div class="card-body">
                                    <div id="chart14"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </b>
    </th>
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
            var grosses = <?php echo json_encode($grosses); ?>;
            var expenses = <?php echo json_encode($expensesdata); ?>;
            var netprofits = <?php echo json_encode($netprofits); ?>;
            
            // chart 14
            Highcharts.chart('chart14', {
                chart: {
                    type: 'column',
                    styledMode: true
                },
                title: {
                    text: "{{trans('navmenu.daily_profit_loss_report')}}"
                },
                xAxis: {
                    categories: labels
                },
                credits: {
                    enabled: false
                },
                series: [{
                    name: "{{trans('navmenu.gross_profit')}}",
                    data: grosses
                }, {
                    name: "{{trans('navmenu.operating_expense')}}",
                    data: expenses
                }, {
                    name: "{{trans('navmenu.profit')}}",
                    data: netprofits
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