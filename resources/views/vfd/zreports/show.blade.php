@extends('layouts.vfd')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-zpays-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-zpay"><a href="{{ url('/home')}}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-zpay active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">

        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row row-cols-1 row-cols-md-1 row-cols-lg-1 row-cols-xl-1">
        <div class="col-md-6 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
            <hr/>
            <div class="card">
                <div class="card-body">
                    <div id="receipt">
                        <center id="top" style="padding-top: 15px;">
                            <h6>***START OF LEGAL ZREPORT***</h6>
                            <hr>
                            <div class="logo">
                                <figure>
                                    <img class="invoice-logo" src="{{asset('vfd/images/tra-logo.jpeg')}}" alt="" style="width: 60px; height: 60px">
                                </figure>
                            </div>
                            <hr>
                            <div class="info" style="text-align: center;"> 
                                <h6>{{$reginfo->name}}<br>
                                    {{$reginfo->address}}<br>
                                    {{$reginfo->country}}<br>
                                    {{$reginfo->street}}<br>
                                    TEL : {{$reginfo->mobile}}<br>
                                    TIN {{$reginfo->tin}}<br>
                                    VRN {{$reginfo->vrn}}<br>
                                    SERIAL NUMBER {{$reginfo->serial}}<br>
                                    UIN {{$reginfo->uin}}<br><br>
                                    {{$reginfo->taxoffice}}
                                </h6>
                                <hr>
                            </div><!--End Info-->
                        </center><!--End InvoiceTop-->
                        
                        <div id="mid">
                            <h6>
                                {{trans('navmenu.date')}} {{date('d-m-Y', strtotime($zreport->date)) }}<span style="float: right;">TIME {{ date('H:i:sA', strtotime($zreport->date))}}</span>
                            </h6>
                            <hr>
                            <h6 style="text-align: center;"><b>DAILY Z REPORT</b></h6>
                            <div>
                                CURRENT Z REPORT: <span style="float: right;">{{$zreport->znum}}</span><br>
                                PREVIOUS Z REPORT: <span style="float: right;">{{ $lastzrdate }}</span><br>
                            </div>
                            <table class="table" width="100%">
                                <tr>
                                    <td>
                                        FIRST RECEIPT<br>
                                        {{ date('d-m-Y', strtotime($firstRct->date))}} <span style="float: right;">{{ date('H:i:s', strtotime($firstRct->date))}}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        LAST RECEIPT<br>
                                        {{ date('d-m-Y', strtotime($lastRct->date))}} <span style="float: right;">{{ date('H:i:s', strtotime($lastRct->date))}}</span>
                                    </td>
                                </tr>
                            </table>
                        </div><!--End Invoice Mid-->
                        
                        <div id="bot">
                            <div id="table">
                                <h6 style="text-align: center;">PAYMENT REPORTS</h6>
                                <table class="table" width="100%">
                                    <tbody>
                                        <?php $tpay = 0; ?>
                                        @foreach($zreportpayments as $key => $zpay)
                                        <?php $tpay += $zpay->pmtamount; ?>
                                        <tr>
                                            <td>{{$zpay->pmttype}}</td>
                                            <td style="text-align: right;">
                                                {{number_format($zpay->pmtamount, 2, '.', "'")}}
                                            </td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td>{{trans('navmenu.total')}}</td>
                                            <td style="text-align: right;">{{number_format($tpay, 2, '.', "'")}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <h6 style="text-align: center;">TAX REPORTS</h6>
                                <table class="table" width="100%">
                                    <tbody>
                                        <?php $tnet = 0; $ttax = 0;?>
                                        @foreach($zreportvattotals as $key => $zvatt)
                                        <?php $tnet += $zvatt->netamount; $ttax += $zvatt->taxamount; ?>
                                        <tr>
                                            <td>TAX {{$zvatt->vatrate}} 
                                                @if($zvatt->vatrate == 'D' || $zvatt->vatrate == 'E')
                                                ({{App\Taxcode::where('code', $zvatt->vatrate)->first()->value}})
                                                @else
                                                ({{number_format(App\Taxcode::where('code', $zvatt->vatrate)->first()->value, 2)}}%)
                                                @endif <br>
                                            NET SUM    <span style="float: right;"> {{number_format($zvatt->netamount, 2, '.', "'")}}</span><br>
                                            TAX <span style="float: right;"> {{number_format($zvatt->taxamount, 2, '.', "'")}}</span>
                                        </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <h6 style="text-align: center;"><b>TOTALS</b></h6>
                                <table class="table" width="100%">
                                    <tbody>
                                        <tr>
                                            <td>TURNOVER <span style="float: right;">{{number_format($tnet+$ttax, 2, '.', "'")}}</span><br>
                                                NET <span style="float: right;">{{number_format($tnet, 2, '.', "'")}}</span><br>
                                                TAX <span style="float: right;">{{number_format($ttax, 2, '.', "'")}}</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div><!--End Table-->
                            <div class="text-center" id="legalcopy" style="text-align: center;">
                                <h6 class="legal">***END OF LEGAL ZREPORT***
                                </h6>
                                <hr>
                            </div>
                        </div><!--End ReceiptBot-->
                    </div><!--End Receipt-->

                    <div class="row align-zpays-center">
                        <div class="col-md-6 p-2">
                            <a href="javascript:;" onclick="javascript:savePdf()"  class="btn btn-success" style="width: 100%;">
                                <i class="bx bx-download"></i> DOWNLOAD
                            </a>
                        </div>
                        <div class="col-md-6 p-2">
                            <button id="btnPrint" type="button" class="btn btn-primary" style="width: 100%;"><i class="bx bx-printer"></i>{{trans('navmenu.print')}}</button>
                        </div>
                    </div>
                    <div class="row align-zpays-center">
                        <div class="col-md-6 p-2">
                            <button class="btn btn-info" style="width: 100%;"><i class="bx bx-share"></i> Share</button>
                        </div>
                        <div class="col-md-6 p-2">
                            <a href="javascript:history.back()" class="btn btn-warning" style="width: 100%;"><i class="bx bx-arrow-to-left"></i>{{trans('navmenu.btn_back')}}</a>
                        </div>           
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
    <link href="https://fonts.cdnfonts.com/css/thegoodmonolith" rel="stylesheet">
                
    <link rel="stylesheet" type="text/css" href="{{ asset('vfd/css/receipt.css') }}">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script>    
        $(document).ready( function ()  {
            $('#btnPrint').on("click", function(e) {

                if ($("#printer").length) {
                    $("#printer").remove();
                }

                var divElements = $("#receipt").html();
                var iframe = $('<iframe class="hidden" id="printer"></iframe>').appendTo('body');
                var printer = $('#printer');
                printer.contents().find('body').append('<!DOCTYPE html><head><title>Print Title</title><link rel="stylesheet" href="../assets/css/bootstrap.min.css"><link href="http://fonts.cdnfonts.com/css/thegoodmonolith" rel="stylesheet"></head><body>' + divElements + '</body>');
                setTimeout(function() {  
                    document.title = "<?php echo trans('navmenu.znum').'_'.$zreport->znum.'.'?>";
                    printer.get(0).contentWindow.print();

                }, 250);
            });
        });

        function savePdf() {
            const element = document.getElementById("receipt");
            var filename = "<?php echo trans('navmenu.znum').'_'.$zreport->znum.'.'?>";
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