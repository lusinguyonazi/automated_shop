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
            <form class="dashform row g-3" action="{{url('dreport-summary')}}" method="POST">
                @csrf
                <div class="col-md-6"></div>
                <input type="hidden" name="start_date" id="start_input" value="">
                <input type="hidden" name="end_date" id="end_input" value="">
                <!-- Date and time range -->
                <div class="col-md-6 mb-3">
                    <div class="input-group">
                        <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange">
                            <span><i class="bx bx-calendar"></i></span>
                            <i class="bx bx-caret-down"></i>
                        </button>
                    </div>
                </div>
            </form>
            <div class="row">
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <div id="chart3"></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <div id="inv-content">
                                <div class="col-xs-12" style="text-align: center; text-transform: uppercase; color: blue">
                                    @if(!is_null($shop->logo_location))
                                    <figure>
                                        <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                                    </figure>
                                    @endif
                                    <h5>{{$shop->name}}</h5>
                                    <h6>{{trans('navmenu.report_summary')}}</h6>
                                    <p>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</p>
                                </div>
                                <div class="col-md-12">
                                    <div class="table-responsive" style="border-top: 2px solid #82B1FF; padding: 35px;">
                                        <table class="table">
                                            <tbody>
                                                <tr>
                                                    <td>{{trans('navmenu.total_sales')}}</td>
                                                    <td style="text-align: right;">{{number_format($total_sales, 2, '.', ',')}}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('navmenu.total_debts')}}</td>
                                                    <td style="text-align: right;">{{number_format($total_debts, 2, '.', ',')}}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('navmenu.cash_payments')}}</td>
                                                    <td style="text-align: right;">{{number_format($total_payments, 2, '.', ',')}}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('navmenu.debt_payments')}}</td>
                                                    <td style="text-align: right;">{{number_format($paid_debts, 2, '.', ',')}}</td>
                                                </tr>
                                                <tr>
                                                    <td><b>{{trans('navmenu.total_payments')}}</b></td>
                                                    <td style="text-align: right;"><b>{{number_format($total_collections, 2, '.', ',')}}</b></td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('navmenu.purchase_payments')}}</td>
                                                    <td style="text-align: right;">{{number_format($purchase_payments, 2, '.', ',')}}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('navmenu.paid_expenses')}}</td>
                                                    <td style="text-align: right;">{{number_format($paid_expenses, 2, '.', ',')}}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{trans('navmenu.cash_out')}}</td>
                                                    <td style="text-align: right;">{{number_format($total_cashout, 2, '.', ',')}}</td>
                                                </tr>
                                                <tr style="border-top: 2px solid #BDBDBD; border-bottom: 2px solid #BDBDBD;">
                                                    <td style="text-transform: uppercase;"><strong>{{trans('navmenu.closing_balance')}}</strong></td>
                                                    <td style="text-align: right;"><strong>{{number_format($closing_balance, 2, '.', ',')}}</strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>      
                        </div>
                        <div class="card-footer">
                            <a href="#" onclick="javascript:printDiv('inv-content')" class="btn btn bg-info btn-sm float-end" style="margin-left: 5px;"><i class="bx bx-printer"></i> {{trans('navmenu.print')}}</a>
                            <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm  float-end"><i class="bx bx-download"></i> Download PDF</a>
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

            var totalsales = <?php echo $total_sales; ?>;
            var totaldebts = <?php echo $total_debts; ?>;
            var cashpay = <?php echo $total_payments; ?>;
            var debtpay = <?php echo $paid_debts; ?>;
            var tcollect = <?php echo $total_collections; ?>;
            var purchpay = <?php echo $purchase_payments; ?>;
            var paidexp = <?php echo $paid_expenses; ?>;
            var cashout = <?php echo $total_cashout; ?>;
            var closebal = <?php echo $closing_balance; ?>;
            var nsales = <?php echo $nsales; ?>;
            var ndebts = <?php echo $ndebts; ?>;
            var tcols = <?php echo $tcols; ?>;
            var paidexps = <?php echo $paidexps; ?>;
            var purchpays = <?php echo $purchpays; ?>;
            var tpaids = <?php echo $tpaids; ?>;
            var tpdebts = <?php echo $tpdebts; ?>;
            var ncouts = <?php echo $ncouts; ?>;
            
            // chart 3
            Highcharts.chart('chart3', {
                chart: {
                    type: 'variablepie',
                    styledMode: true
                },
                credits: {
                    enabled: false
                },
                title: {
                    text: "{{trans('navmenu.report_summary')}}<br> {{$duration}}"
                },
                tooltip: {
                    headerFormat: '',
                    pointFormat: "<span style='color:{point.color}'>\u25CF</span> <b> {point.name}</b><br/>" + "{{trans('navmenu.amount')}}: <b>{point.y}</b><br/>" + "{{trans('navmenu.quantity')}}: <b>{point.z}</b><br/>"
                },
                series: [{
                    minPointSize: 10,
                    innerSize: '20%',
                    zMin: 0,
                    name: "{{trans('navmenu.report_summary')}}",
                    data: [{
                        name: "{{trans('navmenu.total_sales')}}",
                        y: totalsales,
                        z: nsales
                    }, {
                        name: "{{trans('navmenu.total_debts')}}",
                        y: totaldebts,
                        z: ndebts
                    }, {
                        name: "{{trans('navmenu.cash_payments')}}",
                        y: cashpay,
                        z: tpaids
                    }, {
                        name: "{{trans('navmenu.debt_payments')}}",
                        y: debtpay,
                        z: tpdebts
                    }, {
                        name: "{{trans('navmenu.total_payments')}}",
                        y: tcollect,
                        z: tcols
                    }, {
                        name: "{{trans('navmenu.purchase_payments')}}",
                        y: purchpay,
                        z: purchpays
                    }, {
                        name: "{{trans('navmenu.paid_expenses')}}",
                        y: paidexp,
                        z: paidexps
                    }, {
                        name: "{{trans('navmenu.cash_out')}}",
                        y: cashout,
                        z: ncouts
                    }, {
                        name: "{{trans('navmenu.closing_balance')}}",
                        y: closebal,
                        z: 0
                    }]
                }]
            });
        });
    </script>
@endsection

<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
<script type="text/javascript">
    function printDiv(divID) {
        //Get the HTML of div
        var divElements = document.getElementById(divID).innerHTML;
        //Get the HTML of whole page
        var oldPage = document.body.innerHTML;
        //Reset the page's HTML with div's HTML only
        document.body.innerHTML = divElements;
        //File name for printed ducument
        document.title = "<?php echo trans('navmenu.report_summary').'_'.$duration; ?>";
        //Print Page
        window.print();
        //Restore orignal HTML
        document.body.innerHTML = oldPage;
    }

    function savePdf() {
        const element = document.getElementById("inv-content");
        var filename = "<?php echo trans('navmenu.report_summary').'_'.$reporttime; ?>";
        var opt = {
            margin:       0.5,
            filename:     filename+'.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2 },
            jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        // New Promise-based usage:
        html2pdf().set(opt).from(element).save();
    }
</script>