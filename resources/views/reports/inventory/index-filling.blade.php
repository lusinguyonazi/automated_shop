@extends('layouts.app')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $page }}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">

        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-12 mx-auto">
            <form class="row g-3 d-flex justify-content-end dashform" action="{{ url('stock-reports') }}" method="POST">
                @csrf
                <div class="col-md-5 pt-2"></div>
                <input type="hidden" name="start_date" id="start_input" value="{{ $start_date }}">
                <input type="hidden" name="end_date" id="end_input" value="{{ $end_date }}">
                <div class="form-group col-md-4">
                    <select name="product_id" class="form-control form-control-md">
                        @if (!is_null($product))
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @else
                            <option value="">{{ trans('navmenu.select_product') }}</option>
                        @endif
                        @foreach ($products as $prod)
                            <option value="{{ $prod->id }}">{{ $prod->name }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Date and time range -->
                <div class="form-group col-md-3">
                    <div class="input-group form-control-sm d-flex justify-content-end">
                        <button type="button" class="btn btn-sm btn-white btn-sm pull-right" id="reportrange">
                            <span><i class="fa fa-calendar"></i></span>
                            <i class="fa fa-caret-down"></i>
                        </button>
                    </div>
                </div>
            </form>
            <div class="card">
                <div class="card-body">
                    <div id="inv-content">
                        <div
                            style="text-align: center; text-transform: uppercase; color: blue; border-bottom: 3px sold red;">
                            @if (!is_null($shop->logo_location))
                                <figure>
                                    <img class="invoice-logo" src="{{ asset('storage/logos/' . $shop->logo_location) }}"
                                        alt="">
                                </figure>
                            @endif
                            <h4>{{ Session::get('shop_name') }}</h4>
                            <h6 class="title">{{ trans('navmenu.stock_status_report') }}<br><br> <b>
                                    @if (app()->getLocale() == 'en')
                                        {{ $duration }}@else{{ $duration_sw }}
                                    @endif
                                </b>
                            </h6>
                        </div>
                        <div class="invoice-content">
                            <table border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="text-align: left;">{{ trans('navmenu.product_name') }}</th>
                                        <th style="text-align: center;">{{ trans('navmenu.purchased') }}</th>
                                        <th style="text-align: center;">{{ trans('navmenu.sold') }}</th>
                                        <th style="text-align: center;">{{ trans('navmenu.total_g_or_l') }}</th>
                                        <th style="text-align: center;">{{ trans('navmenu.in_stock') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($instocks as $index => $stock)
                                        <tr>
                                            <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">
                                                {{ $stock['name'] }}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">
                                                @if ($stock['stock_in'] - floor($stock['stock_in']) >= 0.01)
                                                    {{ $stock['stock_in'] }}
                                                    @else{{ number_format($stock['stock_in'], 0) }}
                                                @endif
                                            </td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">
                                                @if ($stock['sold'] - floor($stock['sold']) >= 0.01)
                                                    {{ $stock['sold'] }}
                                                    @else{{ number_format($stock['sold'], 0) }}
                                                @endif
                                            </td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">
                                                @if ($stock['damage'] - floor($stock['damage']) >= 0.01)
                                                    {{ $stock['damage'] }}
                                                    @else{{ number_format($stock['damage'], 0) }}
                                                @endif
                                            </td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">
                                                @if ($stock['in_stock'] - floor($stock['in_stock']) >= 0.01)
                                                    {{ $stock['in_stock'] }}
                                                    @else{{ number_format($stock['in_stock'], 0) }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <h3 class="profile-username text-center"><b>{{ trans('navmenu.depth_measures') }}</b></h3>
                            <table border="0" cellspacing="0" cellpadding="0">
                                <thead>
                                    <tr>
                                        <th style="text-align: right; border-bottom: 1px solid #e0e0e0;">
                                            {{ trans('navmenu.date') }}</th>
                                        <th style="text-align: left; border-bottom: 1px solid #e0e0e0;">
                                            {{ trans('navmenu.product_name') }}</th>
                                        <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">
                                            {{ trans('navmenu.depth_measure') }}</th>
                                        <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">
                                            {{ trans('navmenu.in_stock') }}</th>
                                        <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">
                                            {{ trans('navmenu.g_or_l') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($damages as $damage)
                                        <tr>
                                            <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">
                                                {{ date('d-m-Y', strtotime($damage->time_created)) }}</td>
                                            <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">
                                                {{ $damage->name }}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">
                                                {{ $damage->deph_measure }}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">
                                                {{ $damage->in_stock }}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">
                                                {{ -$damage->quantity }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="options" style="margin-top: 5px;">
                        <!-- this row will not appear when printing -->
                        <a href="#" onclick="javascript:printDiv('inv-content')" class="btn btn bg-info float-end"
                            style="margin-left: 5px;"><i class="bx bx-printer"></i> {{ trans('navmenu.print') }}</a>
                        <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-flat float-end"><i
                                class="bx bx-download"></i> Download PDF</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<script src="https://code.jquery.com/jquery-3.4.1.min.js"
    integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js">
</script>
<script language="javascript" type="text/javascript">
    function printDiv(divID) {
        //Get the HTML of div
        var divElements = document.getElementById(divID).innerHTML;
        //Get the HTML of whole page
        var oldPage = document.body.innerHTML;

        //Reset the page's HTML with div's HTML only
        document.body.innerHTML = divElements;


        //File name for printed ducument
        document.title = "<?php echo trans('navmenu.stock_status_report') . '_' . $duration; ?>";

        //Print Page
        window.print();

        //Restore orignal HTML
        document.body.innerHTML = oldPage;
    }

    function savePdf() {
        const element = document.getElementById("inv-content");
        var filename = "<?php echo trans('navmenu.stock_status_report') . '_' . $duration; ?>";
        var opt = {
            margin: 0.5,
            filename: filename + '.pdf',
            image: {
                type: 'jpeg',
                quality: 0.98
            },
            html2canvas: {
                scale: 2
            },
            jsPDF: {
                unit: 'in',
                format: 'letter',
                orientation: 'portrait'
            }
        };

        // New Promise-based usage:
        html2pdf().set(opt).from(element).save();

    }
</script>
