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
        <div class="col-md-4 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
            <hr/>
            <div class="card">
                <div class="card-body">
                    <div id="receipt">
                        <center id="top">
                            <div class="logo">
                                @if(!is_null($shop->logo_location))
                                <figure>
                                    <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="" style="width: 60px; height: 60px">
                                </figure>
                                @endif
                            </div>
                            <div class="info" style="text-align: center;"> 
                                <h5>{{$shop->name}} <br>
                                <small style="font-size: 12px;">{{$shop->short_desc}}</small></h5>
                            </div><!--End Info-->
                        </center><!--End InvoiceTop-->
                        
                        <div id="mid" style="text-align: center; font-size: 12px;">
                            <div class="info">
                                <p>
                                  {{$shop->postal_address}} {{$shop->physical_address}} {{$shop->street}}, {{$shop->district}},{{$shop->city}}<br>
                                  {{trans('navmenu.email')}}   : {{$shop->email}}<br>
                                    {{trans('navmenu.mobile')}}   : {{$shop->mobile}}<br>
                                </p>
                            </div>
                            <h6 style="text-transform: uppercase;">{{trans('navmenu.receipt_no')}}: {{$recno}}</h6>
                            <div>
                                <span>{{trans('navmenu.date')}}: <strong>{{$date}}</strong></span>
                            </div>
                            <div>
                                <span>{{trans('navmenu.customer')}}: <strong>{{$customer->name}}</strong></span><br>
                                <span>{{trans('navmenu.customer_id')}}: <strong>{{sprintf('%03d', $customer->cust_id)}}</strong></span><br>
                                @if($shop->subscription_type_id == 2)
                                <span>{{trans('navmenu.tin')}}: <strong>{{$customer->tin}}</strong></span><br>
                                <span>{{trans('navmenu.vrn')}}: <strong>{{$customer->vrn}}</strong></span><br>
                                <span>{{trans('navmenu.mobile')}}: <strong>{{$customer->phone}}</strong></span>
                                @endif
                            </div>
                        </div><!--End Invoice Mid-->
                        
                        <div id="bot">
                            <div id="table">
                                <table class="table" width="100%" style="font-size: 12px;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: left;">
                                                {{trans('navmenu.description')}}
                                            </th>
                                            <th style="text-align: right;">{{trans('navmenu.total')}} ({{$sale->currency}})</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $key => $item)
                                        <?php
                                            $punit = App\Models\ProductUnit::find($item->product_unit_id);
                                            $quantity_sold = $item->quantity_sold/$punit->qty_equal_to_basic;
                                            $price_per_unit = $item->price_per_unit*$punit->qty_equal_to_basic;
                                            $unit_discount = $item->discount*$punit->qty_equal_to_basic;

                                        ?>
                                        <tr>
                                            <td>{{$item->name}}<br>
                                                <small style="color: gray;">
                                                    @if($settings->show_discounts)
                                                    {{$quantity_sold + 0}} {{$punit->unit_name}} x {{number_format($price_per_unit*$sale->ex_rate, 2, '.', ',')}}
                                                    @else
                                                    {{$quantity_sold}} x {{number_format(($price_per_unit-$discount)*$sale->ex_rate, 2, '.', ',')}}
                                                    @endif
                                                </small>
                                            </td>
                                            <td style="text-align: right;">
                                                @if($settings->show_discounts)
                                                {{number_format($item->price*$sale->ex_rate, 2, '.', ',')}}
                                                @else
                                                {{number_format(($item->price-$item->total_discount)*$sale->ex_rate, 2, '.', ',')}}
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                        @foreach($servitems as $key => $item)
                                        <tr>
                                            <td>{{$item->name}}<br>
                                                <small>{{number_format($item->no_of_repeatition)}} x {{number_format($item->price*$sale->ex_rate, 2, '.', ',')}}</small>
                                            </td>
                                            <td style="text-align: right;">{{number_format($item->total*$sale->ex_rate, 2, '.', ',')}}</td>
                                        </tr>
                                        @endforeach
                                        @if($settings->show_discounts)
                                        <tr style="border-top: 2px dashed #000;">
                                            <th style="text-align: right;">{{trans('navmenu.subtotal')}}</th>
                                            <td style="text-align: right;">{{number_format(($sale->sale_amount-$sale->tax_amount)*$sale->ex_rate, 2, '.', ',')}}</td>
                                        </tr>
                                                
                                        <tr>
                                            <th style="text-align: right;">{{trans('navmenu.discount')}}</th>
                                            <td style="text-align: right;">{{number_format($sale->sale_discount*$sale->ex_rate, 2, '.', ',')}}</td>
                                        </tr>
                                        @else
                                        <tr style="border-top: 2px dashed #000;">
                                            <th style="text-align: right;">{{trans('navmenu.subtotal')}}</th>
                                            <td style="text-align: right;">{{number_format(($sale->sale_amount-$sale->sale_discount-$sale->tax_amount)*$sale->ex_rate, 2, '.', ',')}}</td>
                                        </tr>
                                        @endif    

                                        @if($settings->is_vat_registered && $sale->tax_amount > 0)
                                        <tr>
                                            <th style="text-align: right;">{{trans('navmenu.vat')}}</th>
                                            <td style="text-align: right;">{{number_format($sale->tax_amount*$sale->ex_rate, 2, '.', ',')}}</td>
                                        </tr>
                                        @endif
                                    
                                        <tr class="amount-total">
                                            <th style="text-align: right;">{{trans('navmenu.total')}}</th>
                                            <td style="text-align: right;">{{number_format(($sale->sale_amount-$sale->sale_discount)*$sale->ex_rate, 2, '.', ',')}}</td>
                                        </tr>
                                    
                                        <tr data-hide-on-quote="true">
                                            <th style="text-align: right;">{{trans('navmenu.paid')}}</th>
                                            <td style="text-align: right;">{{number_format($sale->sale_amount_paid*$sale->ex_rate, 2, '.', ',')}}</td>
                                        </tr>
                                    
                                        <tr data-hide-on-quote="true" style="border-bottom: 2px dashed #000;">
                                            <th style="text-align: right;">{{trans('navmenu.debt')}}</th>
                                            <td style="text-align: right;">
                                                @if($sale->sale_amount_paid <= $sale->sale_amount-$sale->sale_discount)
                                                {{number_format(($sale->sale_amount-($sale->sale_discount+$sale->sale_amount_paid))*$sale->ex_rate, 2, '.', ',')}}
                                                @else
                                                {{0}}
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div><!--End Table-->
                            <div style="text-align: center; font-size: 12px;">
                                <span>{{trans('navmenu.pay_method')}}: <strong>
                                @if($sale->pay_type == 'Cash')
                                    @if(app()->getLocale() == 'en')
                                    {{$sale->pay_type}}
                                    @else
                                    {{trans('navmenu.cash')}}
                                    @endif
                                @elseif($sale->pay_type == 'Mobile Money')
                                    @if(app()->getLocale() == 'en')
                                    {{$sale->pay_type}}
                                    @else
                                    {{trans('navmenu.mobilemoney')}}
                                    @endif
                                @elseif($sale->pay_type == 'Bank')
                                    @if(app()->getLocale() == 'en')
                                    {{$sale->pay_type}}
                                    @else
                                    {{trans('navmenu.bank')}}
                                    @endif            
                                @endif</strong></span><br>
                                <span>{{trans('navmenu.issued_by')}}: <strong>{{Auth::user()->first_name}} {{Auth::user()->last_name}}</strong></span>
                            </div>
                            <div id="legalcopy" style="text-align: center; font-size: 12px; border-bottom: 2px dashed #000;">
                                <p class="legal"><strong>{{trans('navmenu.welcome_again')}} </strong>
                                </p>
                            </div>
                        </div><!--End InvoiceBot-->
                    </div><!--End Invoice-->

                    <div class="row align-items-center">
                        <div class="col-md-6 p-2">
                            <button id="btnDownload" type="button" class="btn btn-success btn-sm" style="width: 100%;">
                                <i class="bx bx-download"></i> DOWNLOAD
                            </button>
                        </div>
                        <div class="col-md-6 p-2">
                            <button id="btnPrint" type="button" class="btn btn-primary btn-sm" style="width: 100%;"><i class="bx bx-printer"></i>{{trans('navmenu.print')}}</button>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-md-6 p-2">
                            <button class="btn btn-info btn-sm" style="width: 100%;"><i class="bx bx-share"></i> Share</button>
                        </div>
                        <div class="col-md-6 p-2">
                            <a href="javascript:history.back()" class="btn btn-warning btn-sm" style="width: 100%;"><i class="bx bx-arrow-to-left"></i>{{trans('navmenu.btn_back')}}</a>
                        </div>           
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
    <!-- <link href="http://fonts.cdnfonts.com/css/anonymous-pro" rel="stylesheet">             -->
                
    <link rel="stylesheet" type="text/css" href="{{ asset('css/receipt.css') }}">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js" integrity="sha384-NaWTHo/8YCBYJ59830LTz/P4aQZK1sS0SneOgAvhsIl3zBu8r9RevNg5lHCHAuQ/" crossorigin="anonymous"></script>
    <script>    
        $(document).ready( function ()  {
            $('#btnPrint').on("click", function(e) {

                if ($("#printer").length) {
                    $("#printer").remove();
                }

                var divElements = $("#receipt").html();
                var iframe = $('<iframe class="hidden" id="printer"></iframe>').appendTo('body');
                var printer = $('#printer');
                printer.contents().find('body').append('<!DOCTYPE html><head><title>Print Title</title><link rel="stylesheet" href="../assets/css/bootstrap.min.css"></head><body>' + divElements + '</body>');
                setTimeout(function() {  
                    document.title = "<?php echo trans('navmenu.receipt_no').'_'.$recno.'_'.$date ?>";
                    printer.get(0).contentWindow.print();

                }, 250);
            });
        });
    </script>