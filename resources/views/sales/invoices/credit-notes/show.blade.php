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
    <div class=" col-md-9 mx-auto"> 
        <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
        <hr>
        <div class="card">
            <div class="card-header">
                <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm" style="margin: 5px;"><i class="bx bx-download"></i> Download PDF</a>
                <a href="#" onclick="javascript:printDiv('inv-content')" class="btn btn-secondary btn-sm" style="margin: 5px;"><i class="bx bx-printer"></i> Print</a>
                <a href="{{ route('credit-notes.edit', encrypt($creditnote->id))}}" class="btn btn-primary btn-sm" style="margin: 5px;"><i class="bx bx-edit"></i> Update</a>
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
                            <h2 class="title">{{$shop->name}}</h2>
                            <p>
                                {{$shop->postal_address}} {{$shop->physical_address}}
                                {{$shop->street}} {{$shop->district}}, {{$shop->city}}<br>
                                E-Mail: <a href="#">{{$shop->email}}</a>
                                Tel: <a href="#">{{$shop->mobile}}</a>
                            </p>
                        </div>
                        <div class="company-contact">
                            TIN : {{$shop->tin}}<br>
                            VRN : {{$shop->vrn}}
                        </div>
                    </div>

                    <div class="invoice-content">
                        <table>
                            <tr>
                                <td style="text-align: left;">
                                    <p>CREDIT NOTE TO : <b>{{$creditnote->name}}</b><br>
                                        {{$creditnote->address}}<br>
                                        TIN : {{$creditnote->tin}}<br>
                                        VRN : {{$creditnote->vrn}}<br>
                                        Email :<a href="#">{{$creditnote->email}}</a>
                                        Tel : <a href="#">{{$creditnote->phone}}</a>
                                    </p>
                                </td>
                                <td>
                                    <p style="font-size: 14px; text-transform: uppercase;">
                                    CN No : <strong>{{ sprintf('%04d', $creditnote->credit_note_no)}}</strong><br>
                                    Date : {{date("d, M Y", strtotime($creditnote->time_created))}}<br>
                                    </p>
                                </td>
                            </tr>
                        </table>
                        <div class="title text-center"><h3>CREDIT NOTE</h3></div>
                        <table border="0" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <th class="desc">{{trans('navmenu.description')}}</th>
                                    <th class="qty">{{trans('navmenu.invoice_no')}}</th>
                                    <th class="total">{{trans('navmenu.amount')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="text-align: left;">{{$creditnote->reason}}</td>
                                    <td class="qty">{{ sprintf('%04d', $creditnote->inv_no)}}</td>
                                    <td class="total">{{number_format($creditnote->amount)}}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="no-break">
                            <table class="grand-total">
                                <tbody>
                                    <tr>
                                        <td class="desc"></td>
                                        <td class="qty"></td>
                                        <td class="unit">SUBTOTAL:</td>
                                        <td class="total">{{number_format($creditnote->amount)}}</td>
                                    </tr>
                                    <tr>
                                        <td class="desc"></td>
                                        <td class="unit" colspan="2">GRAND TOTAL ({{$creditnote->currency}}):</td>
                                        <td class="total">{{number_format($creditnote->amount)}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="invoice-footer">
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
            document.title = "<?php echo 'Credit Note_'.sprintf('%06d', $creditnote->credit_note_no).'_'.$creditnote->created_at; ?>";
            
            //Print Page
            window.print();

            //Restore orignal HTML
            document.body.innerHTML = oldPage;
        }

        function savePdf() {
          const element = document.getElementById("inv-content");
          var filename = "<?php echo 'Credit Note_'.sprintf('%06d', $creditnote->credit_note_no).'_'.$creditnote->created_at; ?>";
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