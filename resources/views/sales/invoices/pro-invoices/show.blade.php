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
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-2 p-1">
                            <button onclick="javascript:savePdf()" type="button" class="btn btn-outline-success" style="width: 100%;">
                                <i class="bx bx-download"></i> {{trans('navmenu.download')}}
                            </button>
                        </div>
                        <div class="col-md-2 p-1">
                            <button onclick="javascript:printDiv('inv-content')" type="button" class="btn btn-outline-secondary" style="width: 100%;"><i class="bx bx-printer"></i>{{trans('navmenu.print')}}</button>
                        </div>
                        @if($invoice->status == 'Pending')
                        <div class="col-md-2 p-1">
                            <a class="btn btn-outline-primary" href="{{ route('pro-invoices.edit', encrypt($invoice->id))}}" style="width: 100%"><i class="bx bx-edit"></i>Update</a>
                        </div>
                        <div class="col-md-3 p-1">
                            <a class="btn btn-outline-danger" href="{{url('create-invoice/'.encrypt($invoice->id))}}" style="width: 100%"><i class="bx bx-pencil"></i>Create Invoice</a>
                        </div>
                        @endif
                        <div class="col-md-2 p-1">
                            <a href="javascript:history.back()" class="btn btn-outline-warning" style="width: 100%;"><i class="bx bx-arrow-to-left"></i>{{trans('navmenu.btn_back')}}</a>
                        </div> 
                    </div>
                </div>
                <div class="card-body">
                    <div id="inv-content">
                        <div class="clearfix invoice-header">
                            @if(!is_null($shop->logo_location))
                            <figure>
                                <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                            </figure>
                            @endif
                            <div class="company-address">
                                <h5 class="title">{{$shop->name}} <br>
                                <small style="font-size: 12px;">{{$shop->short_desc}}</small></h5>
                                <p>
                                    {{$shop->postal_address}} {{$shop->physical_address}}
                                    {{$shop->street}} {{$shop->district}}, {{$shop->city}}<br>
                                    E-Mail: <a href="#">{{$shop->email}}</a>
                                    Tel: <a href="#">{{$shop->mobile}}</a>
                                        Web: <a href="#">{{$shop->website}}</a>
                                </p>
                            </div>
                            <div class="company-contact">
                                TIN : {{$shop->tin}}<br>
                                VRN : {{$shop->vrn}}
                            </div>
                        </div>
                        <div class="invoice-content">
                            <div class="details clearfix">
                                <div class="client float-start">
                                    <p>INVOICE TO: {{$customer->name}}</p>
                                    <p>{{$customer->address}}</p>
                                    <a href="#">{{$customer->email}}</a>
                                </div>
                                <div class="data float-end">
                                    @if($invoice->status === 'Finalized' || $invoice->status === 'Sent')
                                    <div class="title">Invoice INV-{{ sprintf('%04d', $invoice->invoice_no)}}</div>
                                    @else
                                    <div class="title">Proforma Invoice </div>
                                    @endif
                                    <div class="date">
                                        Date of Invoice: {{date("F d, Y", strtotime($invoice->time_created))}}<br>
                                        Due Date: <b>{{date("d, M Y", strtotime($invoice->due_date))}}</b>
                                    </div>
                                </div>
                            </div>
                            @if(!is_null($items))
                            <table border="0" cellspacing="0" cellpadding="0">
                                <thead>
                                    <tr>
                                        <th class="desc">Description</th>
                                        <th class="qty">Quantity</th>
                                        <th class="unit">Unit price</th>
                                        <th class="total">Total</th>
                                        @if($settings->is_vat_registered)
                                        <th style="text-align: center;">{{trans('navmenu.vat')}}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $key => $item)
                                    <tr>
                                        <td class="desc" style="border-bottom: 1px solid #e0e0e0;">{{$item->name}}</td>
                                        <td class="qty" style="border-bottom: 1px solid #e0e0e0;">{{$item->quantity}}</td>
                                        <td class="unit" style="border-bottom: 1px solid #e0e0e0;">{{$item->cost_per_unit}}</td>
                                        <td class="total" style="border-bottom: 1px solid #e0e0e0;">{{$item->amount}}</td>
                                        @if($settings->is_vat_registered)
                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;">{{$item->tax_amount}}</td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif
                            @if(!is_null($servitems))
                            <table border="0" cellspacing="0" cellpadding="0">
                                <thead>
                                    <tr>
                                        <th class="desc">Description</th>
                                        <th class="qty">Quantity</th>
                                        <th class="unit">Unit price</th>
                                        <th class="total">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($servitems as $key => $item)
                                    <tr>
                                        <td class="desc"><h4>{{$item->name}}</h4>{{$item->description}}</td>
                                        <td class="qty">{{$item->repeatition}}</td>
                                        <td class="unit">{{$item->cost_per_unit}}</td>
                                        <td class="total">{{$item->amount}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @endif
                            <div class="no-break">
                                <table class="grand-total">
                                    <tbody>
                                        <tr>
                                            <td class="desc"></td>
                                            <td class="qty"></td>
                                            <td class="unit" style="border-bottom: 1px solid #e0e0e0;">SUBTOTAL:</td>
                                            <td class="total" style="border-bottom: 1px solid #e0e0e0;">{{number_format($subtotal)}}</td>
                                        </tr>
                                        <tr>
                                            <td class="desc"></td>
                                            <td class="qty"></td>
                                            <td class="unit" style="border-bottom: 1px solid #e0e0e0;">DISCOUNT:</td>
                                            <td class="total" style="border-bottom: 1px solid #e0e0e0;">{{number_format($invoice->discount)}}</td>
                                        </tr>
                                        <tr>
                                            <td class="desc"></td>
                                            <td class="qty"></td>
                                            <td class="unit" style="border-bottom: 1px solid #e0e0e0;">TAX {{$settings->tax_rate}}%:</td>
                                            <td class="total" style="border-bottom: 1px solid #e0e0e0;">{{number_format($tax)}}</td>
                                        </tr>
                                        <tr>
                                            <td class="desc"></td>
                                            <td class="qty"></td>
                                            <td class="unit" style="border-bottom: 1px solid #e0e0e0;">SHIPPING COSTS:</td>
                                            <td class="total" style="border-bottom: 1px solid #e0e0e0;">{{number_format($invoice->shipping_cost)}}</td>
                                        </tr>
                                        <tr>
                                            <td class="desc"></td>
                                            <td class="qty"></td>
                                            <td class="unit" style="border-bottom: 1px solid #e0e0e0;">ADJUSTMENT:</td>
                                            <td class="total" style="border-bottom: 1px solid #e0e0e0;">{{number_format($invoice->adjustment)}}</td>
                                        </tr>
                                        <tr>
                                            <td class="desc"></td>
                                            <td class="unit" colspan="2" style="border-bottom: 1px solid #e0e0e0;">GRAND TOTAL:</td>
                                            <td class="total" style="border-bottom: 2px solid #e0e0e0;">{{number_format(($grandtotal+$tax+$invoice->shipping_cost+$invoice->adjustment)-$invoice->discount, 2, '.', ',')}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="invoice-footer">
                            <div class="thanks">Thank you!</div>
                            @if(!is_null($invoice->notice))
                            <div class="notice">
                                <div>NOTICE:</div>
                                <div>{{$invoice->notice}}</div>
                            </div>
                            @endif
                            <div class="end">This is an electronic Invoice and is valid without the signature and seal.</div>
                        </div>
                    </div>
                    <div id="editor"></div>
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
            document.title = "<?php echo 'Tax Invoice_'.sprintf('%06d', $invoice->inv_no).'_'.$invoice->created_at; ?>";
            
            //Print Page
            window.print();

            //Restore orignal HTML
            document.body.innerHTML = oldPage;

        }

        function savePdf() {
          const element = document.getElementById("inv-content");
          var filename = "<?php echo 'Tax Invoice_'.sprintf('%06d', $invoice->inv_no).'_'.$invoice->created_at; ?>";
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