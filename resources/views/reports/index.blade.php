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
            <form class="dashform row g-3" action="{{url('both-reports')}}" method="POST">
                @csrf
                <div class="col-md-3">
                @if($settings->is_categorized)
                    <!-- <label class="control-label">{{trans('navmenu.category')}}</label> -->
                    <select name="category_id" id="category" class="form-select form-select-sm mb-3">
                        <option value="">{{trans('navmenu.all_categories')}}</option>
                        @if(!is_null($categories))
                        @foreach($categories as $key => $cat)
                        @if(!is_null($category) && $category->id === $cat->id)
                        <option selected value="{{$cat->id}}">{{$cat->name}}</option>
                        @else
                        <option value="{{$cat->id}}">{{$cat->name}}</option>
                        @endif
                        @endforeach
                        @endif
                    </select>
                @endif
                </div>

                <!-- /.form group -->
                <div class="col-md-3">
                @if($settings->is_service_per_device)
                    <select name="device_id" class="form-select form-select-sm mb-3">
                        <option value="0">{{trans('navmenu.select_device')}}</option>
                        @if(!is_null($devices))
                        @foreach($devices as $dev)
                        <option value="{{$dev->id}}">{{$dev->device_number}}</option>
                        @endforeach                           
                        @endif
                    </select>
                @endif
                </div>
                <input type="hidden" name="start_date" id="start_input" value="">
                <input type="hidden" name="end_date" id="end_input" value="">
                <!-- Date and time range -->
                <div class="col-md-6">
                    <div class="input-group">
                        <button type="button" class="btn btn-white btn-sm float-end" id="reportrange">
                            <span><i class="bx bx-calendar"></i></span>
                            <i class="bx bx-caret-down"></i>
                        </button>
                    </div>
                </div>
            </form>
            <div class="card">
                <div class="card-body">
                    <div id="inv-content">
                        <div class="row">
                            <div class="col-xs-12" style="text-align: center; text-transform: uppercase; color: blue">
                                @if(!is_null($shop->logo_location))
                                <figure>
                                    <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                                </figure>
                                @endif
                                <h5>{{$shop->name}}</h5>
                                @if(!is_null($device))
                                <h6> {{trans('navmenu.gr_report')}} - {{$device->device_number}}<br> <b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></h6>
                                @else
                                <h6>{{trans('navmenu.gr_report')}}<br> <b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></h6>
                                @endif
                            </div>

                            @if($shop->business_type_id != 3)
                            <div class="col-xs-12 invoice-content" style="border-top: 2px solid #82B1FF;">
                                <p class="lead" style="text-transform: uppercase; color: #33691e; font-weight: 200;">{{trans('navmenu.sales')}}:</p>
                                <table border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.product_name')}}</th>
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.qty')}}</th>
                                            @if($shop->business_type_id != 1)
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.buying')}}</th>
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.total')}}</th>
                                            @endif
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.selling')}}</th>
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.total')}}</th>
                                            <!-- <th>Discount</th> -->
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.profit')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sales as $index => $sale)
                                        <tr>
                                            <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{$sale->name}}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">@if($sale->quantity-floor($sale->quantity) >= 0.01){{$sale->quantity}}@else{{number_format($sale->quantity, 0)}}@endif</td>
                                            @if($shop->business_type_id != 1)
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($sale->buying_per_unit, 2, '.', ',')}}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($sale->buying_price, 2, '.', ',')}}</td>
                                            @endif
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format(($sale->price_per_unit-$sale->discount)+$sale->tax, 2, '.', ',')}}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format(($sale->price-$sale->total_discount)+$sale->tax_amount, 2, '.', ',')}}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format((($sale->price-$sale->total_discount)+$sale->tax_amount)-$sale->buying_price, 2, '.', ',')}}</td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td style="text-align: left; border-bottom: 1px solid #e0e0e0;"><strong>{{trans('navmenu.total')}} ({{$defcurr->code}})</strong></td>
                                            <td></td>
                                            @if($shop->business_type_id != 1)
                                            <td></td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"><strong>{{number_format($total_buying, 2, '.', ',')}}/=</strong></td>
                                            @endif
                                            <td></td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"><strong>{{number_format($total_prod_selling, 2, '.', ',')}}/=</strong></td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"><strong>{{number_format($total_gross_profit, 2, '.', ',')}}/=</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                                @if(!($returns->count() < 1))
                                <p class="lead" style="text-transform: uppercase; color: green;">{{trans('navmenu.sales_returns')}}:</p>
                                <table border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.product_name')}}</th>
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.qty')}}</th>
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.buying')}}</th>
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.total')}}</th>
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.selling')}}</th>
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.total')}}</th>
                                            <!-- <th>Discount</th> -->
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.profit')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($returns as $index => $return)
                                        <tr>
                                            <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{$return->name}}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{$return->quantity}}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($return->buying_per_unit, 2, '.', ',')}}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($return->buying_price, 2, '.', ',')}}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($return->price_per_unit-$return->discount, 2, '.', ',')}}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($return->price-$return->total_discount, 2, '.', ',')}}</td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format(($return->price-$return->total_discount)-$return->buying_price, 2, '.', ',')}}</td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td style="text-align: left; border-bottom: 1px solid #e0e0e0;"><strong>{{trans('navmenu.total')}} ({{$defcurr->code}})</strong></td>
                                                <td></td><td></td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"><strong>{{number_format($total_return_buying, 2, '.', ',')}}/=</strong></td>
                                                <td></td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"><strong>{{number_format($total_return_prod_selling, 2, '.', ',')}}/=</strong></td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"><strong>{{number_format($total_return_gross_profit, 2, '.', ',')}}/=</strong></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <p class="lead" style="text-transform: uppercase; color: green;">{{trans('navmenu.turn_over')}}:</p>
                                <table  border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
                                    <thead>
                                        <tr>                                                
                                            <th></th>
                                            <th>{{trans('navmenu.sales')}}</th>
                                            <th>{{trans('navmenu.expense_of_sales')}}</th>
                                            <!-- <th>Discount</th> -->
                                            <th>{{trans('navmenu.profit')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="text-align: left; border-bottom: 1px solid #e0e0e0;"><strong>{{trans('navmenu.total')}} ({{$defcurr->code}})</strong></td>
                                            <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><strong>{{number_format($total_prod_selling-$total_return_prod_selling, 2, '.', ',')}}/=</strong></td>
                                            <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><strong>{{number_format($total_buying-$total_return_buying, 2, '.', ',')}}/=</strong></td>
                                            <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><strong>{{number_format($total_gross_profit-$total_return_gross_profit, 2, '.', ',')}}/=</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                                @endif
                            </div>
                            <!-- /.col -->
                            @endif

                            @if($shop->business_type_id == 3 || $shop->business_type_id == 4)
                            <div class="col-xs-12 invoice-content" style="border-top: 2px solid #82B1FF; padding-top: 10px;">
                                <table border="0" cellspacing="0" cellpadding="0" style="width: 100%;">
                                    <thead style="background:#BDBDBD;">
                                        <tr>
                                            <th style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.service')}}e</th>
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.qty')}}</th>
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.price')}}</th>
                                            <th style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.total')}}</th>
                                            <!-- <th>Discount</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($servsales as $index => $sale)
                                        <tr>
                                            <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{$sale->name}}</td>
                                            <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{$sale->repeatition}}</td>
                                            <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($sale->price-$sale->discount, 2, '.', ',')}}</td>
                                            <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($sale->total-$sale->total_discount, 2, '.', ',')}}</td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td style="text-align: left; border-bottom: 1px solid #e0e0e0;"><strong>{{trans('navmenu.total')}} ({{$defcurr->code}})</strong></td>
                                            <td></td>
                                            <td></td>
                                            <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"><strong>{{number_format($total_serv_selling, 2, '.', ',')}}/=</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.col -->
                            @endif
                        </div>
                        <!-- /.row -->

                        <div class="row"  style="border-top: 2px solid #82B1FF; padding: 25px;">
                            <div class="col-xs-12">
                                <p class="lead" style="text-transform: uppercase; color: #f44336;">{{trans('navmenu.operating_expense')}}:</p>
                                <div class="invoice-content">
                                    <table border="0" cellspacing="0" cellpadding="0">
                                        <thead>
                                            <tr>
                                                <th style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.expense_type')}}</th>
                                                <th style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.amount')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($expenses as $expense)
                                            <tr>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{$expense['expense_type']}}</td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($expense['amount'], 2, '.', ',')}}</td>
                                            </tr>
                                            @endforeach
                                            <tr style="border-top: 2px solid #BDBDBD; border-bottom: 2px solid #BDBDBD;">
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;"><b>{{trans('navmenu.total_expenses')}} ({{$defcurr->code}})</b></td>
                                                <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{number_format($totalexpenses, 2, '.', ',')}}</b></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-xs-12">
                                <p class="lead" style="text-transform: uppercase; color: blue;">{{trans('navmenu.evaluation')}}</p>

                                <div class="invoice-content">
                                    <table border="0" cellspacing="0" cel>
                                        <tr>
                                            <th style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.gross_profit')}} ({{$defcurr->code}}):</th>
                                            <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($gross_profit, 2, '.', ',')}}/=</td>
                                        </tr>
                                        <tr>
                                            <th style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.expenses')}} ({{$defcurr->code}}):</th>
                                            <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($totalexpenses+$shared_expenses, 2, '.', ',')}}/=</td>
                                        </tr>
                                        @if($settings->is_vat_registered)
                                        <tr>
                                            <th style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.vat')}} ({{$defcurr->code}}):</th>
                                            <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($total_vat, 2, '.', ',')}}/=</td>
                                        </tr>
                                        @endif
                                        <tr style="border-bottom: 2px solid gray;">
                                            <th style="text-align: left; border-bottom: 1px solid #e0e0e0;"><b>{{trans('navmenu.net_profit')}} ({{$defcurr->code}}):</b>:</th>
                                            <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{number_format($gross_profit-($totalexpenses+$shared_expenses+$total_vat), 2, '.', ',')}}</b>/=</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <!-- /.col -->
                        </div>
                    </div>
                </div>
                <div class="card-footer options">
                    <a href="#" onclick="javascript:printDiv('inv-content')" class="btn btn bg-info btn-sm float-end" style="margin-left: 5px;"><i class="bx bx-printer"></i> {{trans('navmenu.print')}}</a>
                    <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm  float-end"><i class="bx bx-download"></i> Download PDF</a>
                </div>
            </div>
        </div>
    </div>
@endsection

    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <script language="javascript" type="text/javascript">
        function printDiv(divID) {
            //Get the HTML of div
            var divElements = document.getElementById(divID).innerHTML;
            //Get the HTML of whole page
            var oldPage = document.body.innerHTML;

            //Reset the page's HTML with div's HTML only
            document.body.innerHTML = divElements;


            //File name for printed ducument
            document.title = "<?php echo trans('navmenu.gr_report').'_'.$duration; ?>";
            
            //Print Page
            window.print();

            //Restore orignal HTML
            document.body.innerHTML = oldPage;
        }

        function savePdf() {
          const element = document.getElementById("inv-content");
          var filename = "<?php echo trans('navmenu.gr_report').'_'.$duration; ?>";
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