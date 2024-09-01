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
                    </div>
                </div>
                <div class="card-body">
                    <div id="inv-content">
                        <div class="clearfix invoice-header">
                            <div class="title text-center" style="margin-bottom: 5px; text-transform: uppercase;"><h3>{{trans('navmenu.receipt')}}</h3></div>
                            @if(!is_null($shop->logo_location))
                            <figure>
                                <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                            </figure>
                            @endif
                            <div class="company-address">
                                <h2 class="title">{{$shop->name}} <br>
                                <small style="font-size: 12px;">{{$shop->short_desc}}</small></h2>
                                <p>
                                    {{$shop->postal_address}} {{$shop->physical_address}}
                                    {{$shop->street}} {{$shop->district}}, {{$shop->city}}<br>
                                    E-Mail: <a href="#">{{$shop->email}}</a>
                                    Tel: <a href="#">{{$shop->mobile}}</a>
                                    Web: <a href="#">{{$shop->website}}</a>
                                </p>
                            </div>
                            <div class="company-contact">                    
                                <p style="font-size: 14px; text-transform: uppercase;">
                                    TIN   : <b>{{$shop->tin}}</b><br>
                                    VRN   : <b>{{$shop->vrn}}</b><br>
                                    {{trans('navmenu.receipt_no')}}   : <strong>{{ sprintf('%05d', $accpay->receipt_no)}}</strong><br>
                                    Receipt Date   : <b>{{date("d M, Y", strtotime($accpay->created_at))}}</b><br>
                                </p>
                            </div>
                        </div>
                        <div class="invoice-content">
                            <div class="details clearfix">
                                <div class="client pull-left">
                                    <p>
                                    {{trans('navmenu.customer_name')}} : <b>{{$customer->name}}</b><br>
                                    {{trans('navmenu.customer_id')}} : <b>{{sprintf('%03d', $customer->cust_no)}}</b><br>
                                    {{trans('navmenu.address')}} : <b>{{$customer->address}}</b><br>
                                    TIN : <b>{{$customer->tin}}</b><br>
                                    VRN : <b>{{$customer->vrn}}</b><br>
                                    </p>
                                </div>
                            </div>

                            <table border="0" cellspacing="0" cellpadding="0">
                                <thead>
                                    <tr>
                                        <th style="text-align: left;">{{trans('navmenu.pay_type')}}</th>
                                        <th class="desc">{{trans('navmenu.description')}}</th>
                                        <th class="qty">{{trans('navmenu.invoice_no')}}</th>
                                        <th class="total">{{trans('navmenu.amount')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($accpay->trans_ob_amount > 0)
                                    <tr>
                                        <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.invoice_payment')}}</td>
                                        <td class="desc" style="border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.pay_for')}} {{trans('navmenu.opening_balance')}} {{trans('navmenu.paid_on')}} {{date('d.m.Y', strtotime($accpay->date))}}</td>
                                        <td class="qty" style="border-bottom: 1px solid #e0e0e0;">OB</td>
                                        <td class="total" style="border-bottom: 1px solid #e0e0e0;">{{number_format($accpay->trans_ob_amount)}}</td>
                                    </tr>
                                    @endif
                                    @if($accpay->trans_credit_amount > 0)
                                    <tr>
                                        <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.other_debts')}}</td>
                                        <td class="desc" style="border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.pay_for')}} {{trans('navmenu.other_debts')}} {{trans('navmenu.paid_on')}} {{date('d.m.Y', strtotime($accpay->date))}}</td>
                                        <td class="qty" style="border-bottom: 1px solid #e0e0e0;">COC</td>
                                        <td class="total" style="border-bottom: 1px solid #e0e0e0;">{{number_format($accpay->trans_credit_amount)}}</td>
                                    </tr>
                                    @endif
                                    @foreach($sale_payments as $key => $pay)
                                    <tr>
                                        <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.invoice_payment')}}</td>
                                        <td class="desc" style="border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.pay_for')}} {{date('d M, Y', strtotime($pay->date))}} {{trans('navmenu.paid_on')}} {{date('d.m.Y', strtotime($pay->pay_date))}}</td>
                                        <td class="qty" style="border-bottom: 1px solid #e0e0e0;">{{ sprintf('%04d', $pay->invoice_no)}}</td>
                                        <td class="total" style="border-bottom: 1px solid #e0e0e0;">{{number_format($pay->amount)}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="no-break">
                                <table class="grand-total">
                                    <tbody>
                                        <tr style="border-bottom: 2px solid gray;">
                                            <td class="desc"></td>
                                            <td class="unit" colspan="2">TOTAL ({{$accpay->currency}}):</td>
                                            <td class="total">{{number_format($accpay->payment)}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <h4 style="padding-left: 20px;"><span>Amount in Words : <b>{{$amount_in_words}}</b></span></h4>
                        </div>

                        <div class="invoice-footer" style="text-align: center;">
                            <h4><span class="thanks">Payment Informations</span></h4>
                            <table style="width: 100%; padding: 10px;">
                                <tbody>
                                    <tr>
                                        <td style="text-align: right; width: 50%; padding: 5px; padding-right: 30px;">Mode of Payment :</td>
                                        <td style=" width: 20%; padding: 5px; border-bottom: dotted; 1px #e1f5fe;"><b>{{$accpay->payment_mode}}</b></td>
                                        <td style="width: 30%; padding: 5px;"></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right; width: 50%; padding: 5px; padding-right: 30px;">Cheque/Credit Card/Bank Slip No :</td>
                                        <td style=" width: 20%; padding: 5px; border-bottom: dotted; 1px #e1f5fe;"><b>{{$accpay->cheque_no}}</b></td>
                                        <td style="width: 30%; padding: 5px;"></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right; width: 50%; padding: 5px; padding-right: 30px;">Bank :</td>
                                        <td style=" width: 20%; padding: 5px; border-bottom: dotted; 1px #e1f5fe;"><b>{{$accpay->bank_name}}</b> {{$accpay->bank_branch}}</td>
                                        <td style="width: 30%; padding: 5px;"></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right; width: 50%; padding: 5px; padding-right: 30px;">Cheque/Deposit Date :</td>
                                        <td style=" width: 20%; padding: 5px; border-bottom: dotted; 0.2px #e1f5fe;"><b>{{date('d-m-Y', strtotime($accpay->date))}}</b></td>
                                        <td style="width: 30%; padding: 5px;"></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right; width: 50%; padding: 5px; padding-right: 30px;">Expire Date :</td>
                                        <td style=" width: 20%; padding: 5px; border-bottom: dotted; 0.2px grey;"><b>@if(!is_null($accpay->expire_date)){{date('d M, Y', strtotime($accpay->expire_date))}}@endif</b></td>
                                        <td style="width: 30%; padding: 5px;"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="notice" style="margin-top: 25px;">
                              <!-- <p>
                                <span>_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _</span><br>
                                <span><b>{{trans('navmenu.cashier_signature')}}</b></span>
                              </p> -->
                            </div>
                            <div class="end" style="margin-top: 15[x;">This is an electronic Receipt and is valid without the signature and seal.</div>
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
            document.title = "<?php echo 'Payment Receipt_'.sprintf('%06d', $accpay->receipt_no).'_'.$accpay->created_at; ?>";
            
            //Print Page
            window.print();

            //Restore orignal HTML
            document.body.innerHTML = oldPage;

        }

        function savePdf() {
          const element = document.getElementById("inv-content");
          var filename = "<?php echo 'Payment Receipt_'.sprintf('%06d', $accpay->receipt_no).'_'.$accpay->created_at; ?>";
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