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
                        <div class="col-md-2">
                            <button onclick="javascript:savePdf()" type="button" class="btn btn-outline-success btn-sm" style="width: 100%;">
                                <i class="bx bx-download"></i> {{trans('navmenu.download')}}
                            </button>
                        </div>
                        <div class="col-md-2">
                            <button onclick="javascript:printDiv('inv-content')" type="button" class="btn btn-outline-secondary btn-sm" style="width: 100%;"><i class="bx bx-printer"></i>{{trans('navmenu.print')}}</button>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-info btn-sm" style="width: 100%;"><i class="bx bx-share"></i> Share</button>
                        </div>
                        <div class="col-md-2">
                            <a class="btn btn-outline-primary btn-sm" href="{{ route('invoices.edit', encrypt($invoice->id)) }}" style="width: 100%"><i class="bx bx-edit"></i>Update</a>
                        </div>
                        <div class="col-md-2">
                            <a class="btn btn-outline-danger btn-sm" href="{{ url('create-credit-note/'.encrypt($invoice->id)) }}" style="width: 100%"><i class="bx bx-pencil"></i>Credit Note</a>
                        </div>
                        <div class="col-md-2">
                            <form method="GET" action="{{route('invoices.show', encrypt($invoice->id))}}">
                                <select class="form-select form-select-sm mb-3" name="stmt_currency" onchange="this.form.submit()">
                                    @foreach($stmtcurrencies as $curr)
                                    @if($sale->currency == $curr)
                                    <option selected>{{$curr}}</option>
                                    @else
                                    <option>{{$curr}}</option>
                                    @endif
                                    @endforeach
                                </select>
                            </form>
                        </div> 
                    </div>
                </div>
                <div class="card-body">
                    <div id="inv-content">
                        <div class="clearfix invoice-header">
                            @if($settings->invoice_title_position == 'top')
                            <div class="title text-center" style="margin-bottom: 5px;"><h3>INVOICE</h3></div>
                            @endif
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
                                TIN : <b>{{$shop->tin}}</b>
                                VRN : <b>{{$shop->vrn}}</b><br>
                                E-Mail: <a href="#">{{$shop->email}}</a>
                                Tel: <a href="#">{{$shop->mobile}}</a>
                                Web: <a href="#">{{$shop->website}}</a>
                                </p>
                            </div>
                            <div class="company-contact">
                                @if($settings->invoice_title_position == 'right')
                                <h3>INVOICE</h3>
                                <p style="font-size: 14px; text-transform: uppercase;">
                                Invoice No : <strong>{{ sprintf('%04d', $invoice->inv_no)}}</strong><br>
                                Date : {{date("d, M Y", strtotime($invoice->created_at))}}<br>
                                Due Date: <b>{{date("d, M Y", strtotime($invoice->due_date))}}</b>
                                </p>
                                @else
                                <p style="font-size: 12px; text-transform: uppercase;">
                                  {{trans('navmenu.customer_name')}} : <b>{{$sale->name}}</b><br>
                                  {{trans('navmenu.customer_id')}} : <b>{{ sprintf('%03d', $sale->cust_id)}}</b><br>
                                  TIN : <b>{{$sale->tin}}</b> 
                                  VRN : <b>{{$sale->vrn}}</b><br>
                                  Email :<a href="#"><b>{{$sale->email}}</b></a>
                                  Tel : <a href="#"><b>{{$sale->phone}}</b></a>
                                </p>
                                <p style="font-size: 12px; text-transform: uppercase;">
                                    Invoice No : <strong>{{ sprintf('%06d', $invoice->inv_no)}}</strong><br>
                                    Date : {{date("d, M Y", strtotime($invoice->created_at))}}<br>
                                    Due Date: <b>{{date("d, M Y", strtotime($invoice->due_date))}}</b>
                                </p>
                                @endif
                            </div>
                        </div>

                        <div class="invoice-content">
                            @if($settings->invoice_title_position == 'right')
                            <div class="details clearfix">
                                <div class="client pull-left" style="margin-left: 80px;">
                                    <p style="font-size: 12px; text-transform: uppercase;">
                                    {{trans('navmenu.customer_name')}} : {{$sale->name}}<br>
                                    {{trans('navmenu.customer_id')}} : {{ sprintf('%03d', $sale->cust_id)}}<br>
                                    TIN : {{$sale->tin}} 
                                    VRN : {{$sale->vrn}}<br>
                                    Email :<a href="#" style="text-transform: lowercase;">{{$sale->email}}</a>
                                    Tel : <a href="#">{{$sale->phone}}</a>
                                    </p>
                                </div>
                            </div>
                            @endif
                            @if($stmtcurr == $defcurr)

                            <table border="0" cellspacing="0" cellpadding="0">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">#</th>
                                        <th class="desc">Description</th>
                                        <th char="qty" style="text-align: center;">Quantity</th>
                                        <th class="unit">Unit price</th>
                                        <th class="total">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $tqty = 0; ?>
                                    @foreach($items as $key => $item)
                                    <?php
                                        $punit = App\Models\ProductUnit::find($item->product_unit_id);
                                        $quantity_sold = $item->quantity_sold/$punit->qty_equal_to_basic;
                                        $price_per_unit = $item->price_per_unit*$punit->qty_equal_to_basic;
                                        $unit_discount = $item->discount*$punit->qty_equal_to_basic;
                                        $tqty += $quantity_sold;
                                    ?>
                                    <tr>
                                        <td style="text-align: center; border-bottom: 1px solid #e0e0e0;"> {{$key+1}} </td>
                                        <td class="desc" style="border-bottom: 1px solid #e0e0e0;">{{$item->name}}</td>
                                        <td class="qty" style="border-bottom: 1px solid #e0e0e0;">{{number_format($quantity_sold + 0)}} {{$punit->unit_name}}</td>
                                        @if($settings->show_discounts)
                                        <td class="unit" style="border-bottom: 1px solid #e0e0e0;">{{number_format($price_per_unit, 2, '.', ',')}}</td>
                                        <td class="total" style="border-bottom: 1px solid #e0e0e0;">{{number_format($item->price, 2, '.', ',')}}</td>
                                        @else
                                        <td class="unit" style="border-bottom: 1px solid #e0e0e0;">{{number_format(($price_per_unit-$unit_discount), 2, '.', ',')}}</td>
                                        <td class="total" style="border-bottom: 1px solid #e0e0e0;">{{number_format(($item->price-$item->total_discount), 2, '.', ',')}}</td>
                                        @endif
                                    </tr>
                                    @endforeach

                                    <?php $tsqty = 0; ?>
                                    @foreach($servitems as $key => $servitem)
                                    <?php $tsqty += $servitem->quantity_sold; ?>
                                    <tr>
                                        <td style="text-align: center;"> {{$items->count()+$key+1}} </td>
                                        <td class="desc" style="border-bottom: 1px solid #e0e0e0;">{{$servitem->name}}</td>
                                        <td class="qty" style="border-bottom: 1px solid #e0e0e0;">{{$servitem->quantity_sold}}</td>
                                        <td class="unit" style="border-bottom: 1px solid #e0e0e0;">{{number_format($servitem->price, 2, '.', ',')}}</td>
                                        <td class="total" style="border-bottom: 1px solid #e0e0e0;">{{number_format($servitem->total, 2, '.', ',')}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="no-break">
                                <table class="grand-total">
                                    <tbody>
                                        @if($settings->show_discounts)
                                        <tr>
                                            <td class="desc">{{trans('navmenu.total')}}</td>
                                            <td class="qty" style="text-align: center;">{{$tqty+$tsqty}}</td>
                                            <td class="unit">SUBTOTAL:</td>
                                            <td class="total">{{number_format(($sale->sale_amount-$sale->tax_amount), 2, '.', ',')}}</td>
                                        </tr>
                                        <tr>
                                            <td class="desc"></td>
                                            <td class="qty" style="text-align: center;"></td>
                                            <td class="unit">DISCOUNT:</td>
                                            <td class="total">{{number_format($sale->sale_discount, 2, '.', ',')}}</td>
                                        </tr> 
                                        @else
                                        <tr>
                                            <td class="desc">{{trans('navmenu.total')}}</td>
                                            <td class="qty">{{$tqty+$tsqty}}</td>
                                            <td class="unit">SUBTOTAL:</td>
                                            <td class="total">{{number_format(($sale->sale_amount-$sale->sale_discount-$sale->tax_amount), 2, '.', ',')}}</td>
                                        </tr>
                                        @endif
                                        @if($settings->is_vat_registered && $sale->tax_amount > 0)
                                        <tr>
                                            <td class="desc"></td>
                                            <td class="qty"></td>
                                            <td class="unit">VAT ({{number_format($settings->tax_rate)}}%):</td>
                                            <td class="total">{{number_format($sale->tax_amount, 2, '.', ',')}}</td>
                                        </tr>
                                        @endif
                                        <tr style="border-bottom: 2px solid gray;">
                                            <td class="desc"></td>
                                            <td class="unit" colspan="2">GRAND TOTAL ({{$stmtcurr}}):</td>
                                            <td class="total">{{number_format(($sale->sale_amount-$sale->sale_discount), 2, '.', ',')}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <table border="0" cellspacing="0" cellpadding="0">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">#</th>
                                        <th class="desc">Description</th>
                                        <th char="qty" style="text-align: center;">Quantity</th>
                                        <th class="unit">Unit price</th>
                                        <th class="total">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $tqty = 0; ?>
                                    @foreach($items as $key => $item)
                                    <?php
                                        $punit = App\Models\ProductUnit::find($item->product_unit_id);
                                        $quantity_sold = $item->quantity_sold/$punit->qty_equal_to_basic;
                                        $price_per_unit = $item->price_per_unit*$punit->qty_equal_to_basic;
                                        $unit_discount = $item->discount*$punit->qty_equal_to_basic;
                                        $tqty += $quantity_sold;
                                    ?>
                                    <tr>
                                        <td style="text-align: center;"> {{$key+1}} </td>
                                        <td class="desc" style="border-bottom: 1px solid #e0e0e0;">{{$item->name}}</td>
                                        <td class="qty" style="border-bottom: 1px solid #e0e0e0;">{{number_format($quantity_sold + 0)}} {{$punit->unit_name}}</td>
                                        @if($settings->show_discounts)
                                        <td class="unit" style="border-bottom: 1px solid #e0e0e0;">{{number_format($price_per_unit*$sale->ex_rate, 2, '.', ',')}}</td>
                                        <td class="total" style="border-bottom: 1px solid #e0e0e0;">{{number_format($item->price*$sale->ex_rate, 2, '.', ',')}}</td>
                                        @else
                                        <td class="unit" style="border-bottom: 1px solid #e0e0e0;">{{number_format(($price_per_unit-$unit_discount)*$sale->ex_rate, 2, '.', ',')}}</td>
                                        <td class="total" style="border-bottom: 1px solid #e0e0e0;">{{number_format(($item->price-$item->total_discount)*$sale->ex_rate, 2, '.', ',')}}</td>
                                        @endif
                                    </tr>
                                    @endforeach

                                    <?php $tsqty = 0; ?>
                                    @foreach($servitems as $key => $servitem)
                                    <?php $tsqty += $servitem->quantity_sold; ?>
                                    <tr>
                                        <td style="text-align: center;"> {{$items->count()+$key+1}} </td>
                                        <td class="desc" style="border-bottom: 1px solid #e0e0e0;">{{$servitem->name}}</td>
                                        <td class="qty" style="border-bottom: 1px solid #e0e0e0;">{{$servitem->quantity_sold}}</td>
                                        <td class="unit" style="border-bottom: 1px solid #e0e0e0;">{{number_format($servitem->price*$sale->ex_rate, 2, '.', ',')}}</td>
                                        <td class="total" style="border-bottom: 1px solid #e0e0e0;">{{number_format($servitem->total*$sale->ex_rate, 2, '.', ',')}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="no-break">
                                <table class="grand-total">
                                    <tbody>
                                        @if($settings->show_discounts)
                                        <tr>
                                            <td class="desc">{{trans('navmenu.total')}}</td>
                                            <td class="qty" style="text-align: center;">{{$tqty+$tsqty}}</td>
                                            <td class="unit">SUBTOTAL:</td>
                                            <td class="total">{{number_format(($sale->sale_amount-$sale->tax_amount)*$sale->ex_rate, 2, '.', ',')}}</td>
                                        </tr>
                                        <tr>
                                            <td class="desc"></td>
                                            <td class="qty" style="text-align: center;"></td>
                                            <td class="unit">DISCOUNT:</td>
                                            <td class="total">{{number_format($sale->sale_discount*$sale->ex_rate, 2, '.', ',')}}</td>
                                        </tr> 
                                        @else
                                        <tr>
                                            <td class="desc">{{trans('navmenu.total')}}</td>
                                            <td class="qty">{{$tqty+$tsqty}}</td>
                                            <td class="unit">SUBTOTAL:</td>
                                            <td class="total">{{number_format(($sale->sale_amount-$sale->sale_discount-$sale->tax_amount)*$sale->ex_rate, 2, '.', ',')}}</td>
                                        </tr>
                                        @endif
                                        @if($settings->is_vat_registered && $sale->tax_amount > 0)
                                        <tr>
                                            <td class="desc"></td>
                                            <td class="qty"></td>
                                            <td class="unit">VAT ({{number_format($settings->tax_rate)}}%):</td>
                                            <td class="total">{{number_format($sale->tax_amount*$sale->ex_rate, 2, '.', ',')}}</td>
                                        </tr>
                                        @endif
                                        <tr style="border-bottom: 2px solid gray;">
                                            <td class="desc"></td>
                                            <td class="unit" colspan="2">GRAND TOTAL ({{$stmtcurr}}):</td>
                                            <td class="total">{{number_format(($sale->sale_amount-$sale->sale_discount)*$sale->ex_rate, 2, '.', ',')}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            @endif
                        </div>
                        <div class="invoice-footer">
                            @if($settings->show_bd)
                            <div>
                                <p style="color: #000; font-size: 14px;">
                                    <span class="thanks">Bank Details :</span><br>
                                    @if(!is_null($bankdetail))
                                    Bank Name : <strong>{{$bankdetail->bank_name}}</strong><br>
                                    Swift Code : <strong>{{$bankdetail->swift_code}}</strong><br>
                                    Account Number : <strong>{{$bankdetail->account_number}}</strong><br>
                                    Account Name : <strong>{{$bankdetail->account_name}}</strong>
                                    @else
                                    <span style="color: orange;">Your bank details not updated. Please update your bank details <a href="{{url('shop-details/'.Crypt::encrypt($shop->id))}}">Here</a></span>
                                    @endif
                                </p>
                            </div>
                            @endif
                            @if(!is_null($invoice->note))
                            <div class="notice">
                                <div>NOTICE:</div>
                                <div>{{$invoice->note}}</div>
                            </div>
                            @endif
                            @if($settings->show_end_note)
                                <div class="end">This is an electronic Invoice and is valid without the signature and seal.</div>
                            @endif
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