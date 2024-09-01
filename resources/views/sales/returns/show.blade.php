@extends('layouts.app')
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
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
    <div class=" col-md-10 mx-auto"> 
        <h3>{{$title}}</h3>
        <hr>
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
                    <div class="col-md-2 p-1">
                        <a href="javascript:history.back()" class="btn btn-outline-warning" style="width: 100%;"><i class="bx bx-arrow-to-left"></i>{{trans('navmenu.btn_back')}}</a>
                    </div> 
                </div>
            </div>
            <div class="card-body">
                <div id="inv-content">
                    <div class="clearfix invoice-header">
                        <div class="top-head text-center">
                            <h3>{{trans('navmenu.sales_returns')}}</h3>
                        </div>
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
                            TIN : {{$shop->tin}}<br>
                            VRN : {{$shop->vrn}}
                        </div>
                    </div>

                    <div class="invoice-content" style="padding: 10px;">
                        <div class="details clearfix">
                            <div class="client pull-left">
                                <p style="text-transform: uppercase;">{{trans('navmenu.from')}} (CUSTOMER):</p>
                                <p class="name">{{$salereturn->name}}</p>
                                <p>{{$salereturn->postal_address}} {{$salereturn->physical_address}} {{$salereturn->sreet}}</p>
                                <a href="#">{{$salereturn->email}}</a>
                                <a href="#">{{$salereturn->phone}}</a>
                            </div>
                            <div class="data pull-right">
                                <div class="date">
                                    <p>{{trans('navmenu.date')}} : <b>{{date("d, M Y", strtotime($salereturn->created_at))}}</b></p>
                                </div>
                            </div>
                        </div>
                        <table border="0" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th class="desc">Description</th>
                                    <th class="qty">Quantity</th>
                                    <th class="unit">Unit price</th>
                                    <th class="total">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sritems as $key => $item)
                                <tr>
                                    <td style="border-bottom: 1px solid #e0e0e0;"> {{$key+1}} </td>
                                    <td class="desc" style="border-bottom: 1px solid #e0e0e0;">{{$item->name}}</td>
                                    <td class="qty" style="border-bottom: 1px solid #e0e0e0;">{{number_format($item->quantity)}}</td>
                                    <td class="unit" style="border-bottom: 1px solid #e0e0e0;">{{number_format($item->price_per_unit)}}</td>
                                    <td class="total" style="border-bottom: 1px solid #e0e0e0;">{{number_format($item->price-$item->tax_amount)}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="no-break">
                            <table class="grand-total">
                                <tbody>
                                    <tr>
                                        <td class="desc"></td>
                                        <td class="qty"></td>
                                        <td class="unit">SUBTOTAL:</td>
                                        <td class="total">{{number_format($total-$tax)}}</td>
                                    </tr>
                                    <tr>
                                        <td class="desc"></td>
                                        <td class="qty"></td>
                                        <td class="unit">DISCOUNT:</td>
                                        <td class="total">{{number_format($discount)}}</td>
                                    </tr>

                                    @if($settings->is_vat_registered)
                                    <tr>
                                        <td class="desc"></td>
                                        <td class="qty"></td>
                                        <td class="unit">TAX ({{$settings->tax_rate}}%):</td>
                                        @if($tax > 0)
                                        <td class="total">{{number_format($tax)}}</td>
                                        @else
                                        <td class="total">0</td>
                                        @endif
                                    </tr>
                                    @endif
                                    <tr>
                                        <td class="desc"></td>
                                        <td class="unit" colspan="2">GRAND TOTAL:</td>
                                        <td class="total">{{number_format($total-$discount)}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="invoice-footer">
                        <div class="notice">
                            <div>REASON:</div>
                            <div>{{$salereturn->reason}}</div>
                        </div>
                        <div class="thanks">Thank you!</div>
                        <div class="end">This is an electronic Credit Note and is valid without the signature and seal.</div>
                    </div>
                </div>
                <div id="editor"></div>          
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
            document.title = "<?php echo 'Sale Return_'.'_'.$salereturn->created_at; ?>";
            
            //Print Page
            window.print();

            //Restore orignal HTML
            document.body.innerHTML = oldPage;

        }

        function savePdf() {
          const element = document.getElementById("inv-content");
          var filename = "<?php echo 'Sale Return_'.'_'.$salereturn->created_at; ?>";
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