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
        <div class="col-xl-11 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
            <hr>
            <form class="dashform row g-3" action="{{url('invoice-reports')}}" method="POST" id="stockform">
                @csrf
                <div class="form-group col-sm-6">
                    <select name="customer_id" class="form-control select2" onchange="this.form.submit()">
                        @if(!is_null($customer))
                        <option value="{{$customer->id}}">{{$customer->name}}</option>
                        <option value="">{{trans('navmenu.select_by_customer')}}</option>
                        @else
                        <option value="">{{trans('navmenu.select_by_customer')}} </option>
                        @endif
                        @foreach($customers as $cust)
                        <option value="{{$cust->id}}">{{$cust->name}}</option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="start_date" id="start_input" value="">
                <input type="hidden" name="end_date" id="end_input" value="">
                <!-- Date and time range -->
                <div class="form-group col-sm-6">
                    <div class="input-group">
                        <button type="button" class="btn btn-white pull-right" id="reportrange"><span><i class="bx bx-calendar"></i></span><i class="fa fa-caret-down"></i></button>
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
                                    <div class="tab-title">{{trans('navmenu.invoices')}} (Excel)</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#report-pdf" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-export font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">{{trans('navmenu.invoices_with_items')}}</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" href="{{url('aging-report')}}" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-export font-18 me-1'></i>
                                    </div>
                                    <div class="tab-title">{{trans('navmenu.aging_report')}}</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="report-excel" role="tabpanel">
                            <div class="row">
                                <div class="col-xs-12" style="text-align: center; text-transform: uppercase; color: blue; border-bottom: 2px solid #82B1FF; margin-bottom: 8px;">
                                    @if(!is_null($shop->logo_location))
                                    <figure>
                                        <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                                    </figure>
                                    @endif
                                    <h5>{{$shop->name}}</h5>
                                    <h6 class="title">
                                        {{trans('navmenu.invoice_report')}} <br><br>
                                        {{trans('navmenu.name')}} : 
                                        @if(!is_null($customer))<b>{{$customer->name}}</b>
                                        @else
                                        <b>{{trans('navmenu.all')}}</b>
                                        @endif
                                        <br><br> 
                                        <b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></h6>
                                </div>
                            </div>
                            <!-- Table row -->
                            <div class="row">
                                <div class="col-xs-12 table-responsive">
                                    <table id="all-invoices" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.date')}}</th>
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.customer_id')}}</th>
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.customer_name')}}</th>
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.invoice_no')}}</th>
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.amount')}}</th>
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.due_date')}}</th>
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.status')}}</th>
                                                <!-- <th>{{trans('navmenu.check_no')}}</th> -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($allinvoices as $index => $invoice)
                                            <tr>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{date('d M, Y', strtotime( $invoice->created_at))}}</td>
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{sprintf('%03d', $invoice->cust_no)}}</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{$invoice->name}}</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{sprintf('%04d', $invoice->invoiceno)}}</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format($invoice->amount-$invoice->discount, 0, '.', ',')}}</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{date('d M, Y', strtotime($invoice->due_date))}}</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{$invoice->status}}</td>
                                                <!-- <td></td> -->
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th></th>
                                                <th style="text-align: right; text-transform: uppercase;">{{trans('navmenu.total')}}</th>
                                                <th></th>
                                                <th></th>
                                                <th style="text-align: center;">{{number_format($total)}}/=</th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <!-- /.col -->
                            </div>
                            <!-- /.row -->
                        </div>
                        <div class="tab-pane fade" id="report-pdf" role="tabpanel">
                            <div id="inv-content">
                                 <div class="clearfix invoice-header">
                                    <div class="title text-center" style="margin-bottom: 5px;"><h3>{{trans('navmenu.invoice_report')}}</h3></div>
                                    @if(!is_null($shop->logo_location))
                                    <figure>
                                        <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                                    </figure>
                                    @endif
                                    <div class="company-address">
                                        <h2 class="title">{{$shop->name}} <br>
                                        <small style="font-size: 12px;">{{$shop->short_desc}}</small></h2>
                                        <p style="font-size: 11px;">
                                            {{$shop->postal_address}} {{$shop->physical_address}}
                                            {{$shop->street}} {{$shop->district}}, {{$shop->city}}<br>
                                            <b>E-Mail</b> : <a href="#">{{$shop->email}}</a>
                                            <b>Tel</b> : <a href="#">{{$shop->mobile}}</a>
                                            <b>Web</b> : <a href="#">{{$shop->website}}</a><br>
                                            <b>TIN</b> : {{$shop->tin}} 
                                            <b>VRN</b> : {{$shop->vrn}}
                                        </p>
                                    </div>
                                    <div class="company-contact">
                                        @if(!is_null($customer))
                                        <p style="font-size: 12px; text-transform: uppercase;">
                                            {{trans('navmenu.customer_name')}} : {{$customer->name}}<br>
                                            {{trans('navmenu.customer_id')}} : {{ sprintf('%03d', $customer->cust_id)}}<br>
                                            TIN : {{$customer->tin}} 
                                            VRN : {{$customer->vrn}}<br>
                                            Email :<a href="#">{{$customer->email}}</a>
                                            Tel : <a href="#">{{$customer->phone}}</a>
                                        </p>
                                        @else
                                        <p style="font-size: 12px; text-transform: uppercase;">{{trans('navmenu.all')}}</p>
                                        @endif
                                        <p style="font-size: 12px; text-transform: uppercase;">
                                            <b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b>
                                        </p>
                                    </div>
                                </div>
                                <div class="invoice-content">
                                    <table border="0" cellspacing="0" cellpadding="0">
                                        <thead>
                                            <tr>
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.date')}}</th>
                                                @if($settings->is_filling_station)
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.name')}}</th>
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.vehicle_no')}}</th>
                                                @endif
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.particular')}}</th>
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.quantity')}}</th>
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.price')}}</th>
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.total')}}</th>
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.invoice_no')}}O</th>
                                                <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.due_date')}}</th>
                                                <!-- <th>{{trans('navmenu.check_no')}}</th> -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($invoices as $index => $invoice)
                                            <tr>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{date('d-m-Y', strtotime( $invoice->date))}}</td>
                                                @if($settings->is_filling_station)
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{$invoice->customer}}</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{$invoice->vehicle_no}}</td>
                                                @endif
                                                <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{$invoice->name}}</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{$invoice->qty}}</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format(($invoice->price-$invoice->discount), 2, '.', ',')}}</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{number_format((($invoice->price-$invoice->discount)*$invoice->qty), 2, '.', ',')}}</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{sprintf('%04d', $invoice->inv_no)}}</td>
                                                <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{$invoice->due_date}}</td>
                                                <!-- <td></td> -->
                                            </tr>
                                            @endforeach
                                            <tr >
                                                <td></td>
                                                <td style="text-align: right; text-transform: uppercase; font-size: 14px;"><b>{{trans('navmenu.total')}}</b></td>
                                                <td></td>
                                                <td></td>
                                                <td style="text-align: center; font-size: 14px;"><b>{{number_format($total)}}</b>/=</td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="invoice-footer">
                                    <div class="end">This is an electronic Statement and is valid without the signature and seal.</div>
                                </div>
                            </div>
                            <a href="#" onclick="javascript:savePdf()" class="btn bg-warning" style="margin: 5px;"><i class="bx bx-download"></i> Download PDF</a>
                            <a href="#" onclick="javascript:printDiv('inv-content')" class="btn btn-secondary pull-right" style="margin: 5px;"><i class="bx bx-printer"></i> Print</a>
                
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous"></script>
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
            document.title = "<?php echo trans('navmenu.debtor_account_stmt').'_'.$reporttime; ?>";
            
            //Print Page
            window.print();

            //Restore orignal HTML
            document.body.innerHTML = oldPage;

        }

        function savePdf() {
          const element = document.getElementById("inv-content");
          var filename = "<?php echo trans('navmenu.debtor_account_stmt').'_'.$reporttime; ?>";
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