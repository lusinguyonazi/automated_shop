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
            <form class="dashform row g-3" action="{{url('cash-flow-statement')}}" method="POST">
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
            <div class="card">
                <div class="card-body" style="padding: 35px;">
                    <div id="inv-content">
                        <div class="col-md-12" style="text-align: center; text-transform: uppercase; color: blue">
                            @if(!is_null($shop->logo_location))
                            <figure>
                                <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                            </figure>
                            @endif
                            <h5>{{$shop->name}}</h5>
                            <h6>{{trans('navmenu.cash_flow_stmt')}}<br> <b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></h6>
                        </div>

                        <div class="col-md-12 text-center" style="border-top: 2px solid #82B1FF;">
                            <p class="p-2" style="text-transform: uppercase; color: blue; font-weight: bold;">{{trans('navmenu.cash_inflow')}}:</p>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead style="background: #dcdcde">
                                        <th>{{trans('navmenu.account')}}</th>
                                        <th style="text-align: right;">{{trans('navmenu.amount')}}</th>
                                    </thead>
                                    <tbody>
                                        @foreach($cashins as $key => $cin)
                                        <tr>
                                            <td>
                                              @if($cin['account'] == 'Cash')
                                                @if(app()->getLocale() == 'en')
                                                  {{$cin['account']}}
                                                @else
                                                {{trans('navmenu.cash')}}
                                              @endif
                                              @elseif($cin['account'] == 'Mobile Money')
                                                @if(app()->getLocale() == 'en')
                                                  {{$cin['account']}}
                                                @else
                                                  {{trans('navmenu.mobilemoney')}}
                                                @endif
                                              @elseif($cin['account'] == 'Bank')
                                                @if(app()->getLocale() == 'en')
                                                  {{$cin['account']}}
                                                @else
                                                  {{trans('navmenu.bank')}}
                                                @endif                           
                                              @endif
                                            </td>
                                            <td style="text-align: right;">{{number_format($cin['amount'], 2, '.', ',')}}</td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td><strong>{{trans('navmenu.total')}}</strong></td>
                                            <td style="text-align: right;"><strong>{{number_format($total_cashin, 2, '.', ',')}}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-xs-12 text-center">
                            <p class="p-2" style="text-transform: uppercase; color: red; font-weight: bold;">{{trans('navmenu.cash_outflow')}}:</p>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead style="background: #dcdcde">
                                        <th>{{trans('navmenu.account')}}</th>
                                        <th style="text-align: right;">{{trans('navmenu.amount')}}</th>
                                    </thead>
                                    <tbody>
                                        @foreach($cashouts as $key => $cout)
                                        <tr>
                                            <td>
                                              @if($cout['account'] == 'Cash')
                                                @if(app()->getLocale() == 'en')
                                                  {{$cout['account']}}
                                                @else
                                                {{trans('navmenu.cash')}}
                                              @endif
                                              @elseif($cout['account'] == 'Mobile Money')
                                                @if(app()->getLocale() == 'en')
                                                  {{$cout['account']}}
                                                @else
                                                  {{trans('navmenu.mobilemoney')}}
                                                @endif
                                              @elseif($cout['account'] == 'Bank')
                                                @if(app()->getLocale() == 'en')
                                                  {{$cout['account']}}
                                                @else
                                                  {{trans('navmenu.bank')}}
                                                @endif                           
                                              @endif
                                            </td>
                                            <td style="text-align: right;">{{number_format($cout['amount'], 2, '.', ',')}}</td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td><strong>{{trans('navmenu.total')}}</strong></td>
                                            <td style="text-align: right;"><strong>{{number_format($total_cashout, 2, '.', ',')}}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-xs-12 text-center">
                            <p class="p-2" style="text-transform: uppercase; color: green; font-weight: bold;">{{trans('navmenu.account_balance')}}:</p>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead style="background: #dcdcde">
                                        <th>{{trans('navmenu.account')}}</th>
                                        <th style="text-align: right;">{{trans('navmenu.amount')}}</th>
                                    </thead>
                                    <tbody>
                                        @foreach($cashin_outs as $key => $cashbal)
                                        <tr>
                                            <td>
                                              @if($cashbal['account'] == 'Cash')
                                                @if(app()->getLocale() == 'en')
                                                  {{$cashbal['account']}}
                                                @else
                                                {{trans('navmenu.cash')}}
                                              @endif
                                              @elseif($cashbal['account'] == 'Mobile Money')
                                                @if(app()->getLocale() == 'en')
                                                  {{$cashbal['account']}}
                                                @else
                                                  {{trans('navmenu.mobilemoney')}}
                                                @endif
                                              @elseif($cashbal['account'] == 'Bank')
                                                @if(app()->getLocale() == 'en')
                                                  {{$cashbal['account']}}
                                                @else
                                                  {{trans('navmenu.bank')}}
                                                @endif                           
                                              @endif
                                            </td>
                                            <td style="text-align: right;">{{number_format($cashbal['amount'], 2, '.', ',')}}</td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td><strong>{{trans('navmenu.total')}}</strong></td>
                                            <td style="text-align: right;"><strong>{{number_format($total_balance, 2, '.', ',')}}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3 pt-2 options" style="margin-top: 5px;">
                        <a href="#" onclick="javascript:printDiv('inv-content')" class="btn btn bg-info btn-sm float-end" style="margin-left: 5px;"><i class="bx bx-printer"></i> {{trans('navmenu.print')}}</a>
                        <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm  float-end"><i class="bx bx-download"></i> Download PDF</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
        document.title = "<?php echo trans('navmenu.cash_flow_stmt').'_'.$duration; ?>";
        //Print Page
        window.print();
        //Restore orignal HTML
        document.body.innerHTML = oldPage;
    }

    function savePdf() {
        const element = document.getElementById("inv-content");
        var filename = "<?php echo trans('navmenu.cash_flow_stmt').'_'.$reporttime; ?>";
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