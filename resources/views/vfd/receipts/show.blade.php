@extends('layouts.vfd')

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

    <div class="row row-cols-1 row-cols-md-1 row-cols-lg-1 row-cols-xl-1">
        <div class="col-md-5 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
            <hr/>
            <div class="card">
                <div class="card-body">
                    <div id="receipt">
                        <center id="top" style="padding-top: 5px;">
                            <h6>***START OF LEGAL RECEIPT***</h6>
                            <hr>
                            <div class="logo">
                                <figure>
                                    <img class="invoice-logo" src="{{asset('vfd/images/tra-logo.jpeg')}}" alt="" style="width: 60px; height: 60px">
                                </figure>
                            </div>
                            <hr>
                            <div class="info" style="text-align: center;"> 
                                <h6>{{$reginfo->name}}<br>
                                    {{$reginfo->address}}
                                    {{$reginfo->country}}<br>
                                    {{$reginfo->street}}<br>
                                    <b>TEL</b> {{$reginfo->mobile}}<br>
                                    <b>TIN</b> {{$reginfo->tin}}<br>
                                    <b>VRN</b> {{$reginfo->vrn}}<br>
                                    <b>SERIAL NUMBER</b> {{$reginfo->serial}}<br>
                                    <b>UIN</b> {{$reginfo->uin}}<br><br>
                                    {{$reginfo->taxoffice}}
                                </h6>
                                <hr>
                            </div><!--End Info-->
                        </center><!--End InvoiceTop-->
                        
                        <div id="mid" style="text-align: center;">
                            <h6>{{trans('navmenu.receipt_no')}}: {{$vfdreceipt->rctnum}}<br>ZNo  {{$vfdreceipt->znum}}<br>
                            <span>{{trans('navmenu.date')}}: {{ date("d, M Y H:i:sA", strtotime($vfdreceipt->date))}}</span></h6>
                            <hr>
                            <h6>
                                <span>{{trans('navmenu.customer')}}: {{$vfdreceipt->custname}}</span><br>
                                <span>{{trans('navmenu.cust_id_type')}}: @foreach($custids as $cidt)
                                    @if($cidt['id'] == $vfdreceipt->custidtype)
                                    {{$cidt['name']}}@endif
                                @endforeach</span><br>
                                <span>{{trans('navmenu.customer_id')}}: {{$vfdreceipt->custid}}</span><br>
                                <span>{{trans('navmenu.mobile')}}: {{$vfdreceipt->mobilenum}}</span>
                            </h6>
                        </div><!--End Invoice Mid-->
                        
                        <div id="bot">
                            <div id="table">
                                <table class="table" width="100%">
                                    <thead>
                                        <tr>
                                            <td style="text-align: left;">
                                                {{trans('navmenu.description')}}
                                            </td>
                                            <td style="text-align: right;">{{trans('navmenu.total')}}</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rctitems as $key => $item)
                                        <tr>
                                            <td>{{$item->desc}}<br>
                                                <small>
                                                    {{$item->qty+0}} x {{number_format($item->amt/$item->qty)}}
                                                </small>
                                            </td>
                                            <td style="text-align: right;">
                                                {{number_format($item->amt, 2, '.', ',')}}
                                            </td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td style="text-align: right;">{{trans('navmenu.total_tax_excl')}}</td>
                                            <td style="text-align: right;">{{number_format($vfdreceipt->total_tax_excl, 2, '.', ',')}}</td>
                                        </tr>
                                        
                                        <tr>
                                            <td style="text-align: right;">{{trans('navmenu.total_tax')}}</td>
                                            <td style="text-align: right;">{{number_format($vfdreceipt->taxamount, 2, '.', ',')}}</td>
                                        </tr>
                                    
                                        <tr class="amount-total">
                                            <td style="text-align: right;">{{trans('navmenu.total_tax_incl')}}</td>
                                            <td style="text-align: right;">{{number_format($vfdreceipt->total_tax_incl, 2, '.', ',')}}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right;">{{trans('navmenu.discount')}}</td>
                                            <td style="text-align: right;">{{number_format($vfdreceipt->discount, 2, '.', ',')}}</td> 
                                        </tr>
                                        <tr>
                                            <td style="text-align: right;">{{trans('navmenu.total_payable')}}</td>
                                            <td style="text-align: right;">{{number_format($vfdreceipt->total_tax_incl-$vfdreceipt->discount, 2, '.', ',')}}</td>
                                        </tr>
                                        <tr data-hide-on-quote="true">
                                            <td style="text-align: right;">{{trans('navmenu.pmttype')}}</td>
                                            <td style="text-align: right;">
                                                {{$pmttypes}}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div><!--End Table-->
                            <div class="text-center" id="legalcopy" style="text-align: center;">
                                <h6>Receipt Verification code <br> <b>{{$vfdreceipt->rctvnum}}</b></h6>
                                <hr>
                                {{QrCode::generate('https://virtual.tra.go.tz/efdmsRctVerify/'.$vfdreceipt->rctvnum.'_'.date('His', strtotime($vfdreceipt->date)))}}
                                <hr>
                                <h6 class="legal">***END OF LEGAL RECEIPT***</h6>
                            </div>
                        </div><!--End ReceiptBot-->
                    </div><!--End Receipt-->

                    <div class="row align-items-center">
                        <div class="col-md-6 p-2">
                            <a href="javascript:;" onclick="javascript:savePdf()"  class="btn btn-success" style="width: 100%;">
                                <i class="bx bx-download"></i> DOWNLOAD
                            </a>
                        </div>
                        <div class="col-md-6 p-2">
                            <button id="btnPrint" type="button" class="btn btn-primary" style="width: 100%;"><i class="bx bx-printer"></i>{{trans('navmenu.print')}}</button>
                        </div>
                    </div>
                    <div class="row align-items-center">
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
                printer.contents().find('body').append('<!DOCTYPE html><head><title>Print Title</title><link rel="stylesheet" href="../vfd/css/bootstrap.min.css"><link href="http://fonts.cdnfonts.com/css/thegoodmonolith" rel="stylesheet"><link rel="stylesheet" type="text/css" href="../vfd/css/receipt.css"></head><body>' + divElements + '</body>');
                setTimeout(function() {  
                    document.title = "<?php echo trans('navmenu.receipt_no').'_'.$vfdreceipt->rctvnum.'.'?>";
                    printer.get(0).contentWindow.print();

                }, 250);
            });
        });

        function savePdf() {
          const element = document.getElementById("receipt");
          var filename = "<?php echo trans('navmenu.receipt_no').'_'.$vfdreceipt->rctvnum.'.'?>";
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