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
    <div class=" col-md-10 mx-auto"> 
        <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
        <hr>
        <div class="card">
            <div class="card-header">
                <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm" style="margin: 5px;"><i class="bx bx-download"></i> Download PDF</a>
                <a href="#" onclick="javascript:printDiv('inv-content')" class="btn btn-secondary btn-sm" style="margin: 5px;"><i class="bx bx-printer"></i> Print</a>
                <a href="{{ route('delivery-notes.edit', encrypt($dnote->id))}}" class="btn btn-primary btn-sm" style="margin: 5px;"><i class="bx bx-edit"></i> Update</a>
            </div>
            <div class="card-body">
                <div id="inv-content">
                    <div class="clearfix invoice-header">
                        <div class="title text-center" style="margin-bottom: 5px;"><h3>DELIVERY NOTE</h3></div>
                        @if(!is_null($shop->logo_location))
                        <figure>
                          <img class="dnote-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
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
                            <p style="font-size: 12px; text-transform: uppercase;">
                                {{trans('navmenu.customer_name')}} : <b>{{$sale->name}}</b><br>
                                {{trans('navmenu.customer_id')}} : <b>{{ sprintf('%03d', $sale->cust_no)}}</b><br>
                                TIN : <b>{{$sale->tin}}</b> 
                                VRN : <b>{{$sale->vrn}}</b><br>
                                Email :<a href="#" style="text-transform: lowercase;"><b>{{$sale->email}}</b></a>
                                Tel : <a href="#"><b>{{$sale->phone}}</b></a>
                            </p>
                            <p style="font-size: 14px; text-transform: uppercase;">
                                DN No : <strong>{{ sprintf('%04d', $dnote->note_no)}}</strong><br>
                                Issue Date : {{date("d, M Y", strtotime($dnote->created_at))}}
                            </p>
                        </div>
                    </div>

                    <div class="invoice-content">
                        <table border="0" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <th class="del" style="text-align: center;">#</th>
                                    <th class="desc">Description</th>
                                    <th char="qty" style="text-align: center;">Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $key => $item)
                                <tr>
                                    <td class="del" style="text-align: center;"> {{$key+1}} </td>
                                    <td class="desc" style="border-bottom: 1px solid #e0e0e0;">@if(!is_null($shop->products()->where('product_id', $item->product_id)->first())){{$shop->products()->where('product_id', $item->product_id)->first()->product_no}} - @endif {{$item->name}}</td>
                                    <td class="qty" style="border-bottom: 1px solid #e0e0e0;">@if($item->quantity_sold-floor($item->quantity_sold) >= 0.01){{$item->quantity_sold}}@else{{number_format($item->quantity_sold, 0)}}@endif</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="invoice-footer" style="margin-top: 20px;">
                        @if(!is_null($dnote->comments))
                        <div class="notice text-center">
                        <!-- <div>COMMENTS:</div> -->
                            <div><b>***</b>{{$dnote->comments}}<b>***</b></div>
                        </div>
                        @endif
                        <div class="text-center">
                            <p><span style="text-transform: uppercase; font-size: 18px; font-weight: bold;">{{trans('navmenu.issued_by')}}</span><br>
                                {{trans('navmenu.name')}} : <strong>{{$user->first_name}} {{$user->last_name}}</strong><br>
                                {{trans('navmenu.signature')}} <strong>.....................</strong>
                            </p>
                        </div>
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
            document.title = "<?php echo 'Delivery Note_'.sprintf('%06d', $dnote->note_no).'_'.$dnote->created_at; ?>";
            
            //Print Page
            window.print();

            //Restore orignal HTML
            document.body.innerHTML = oldPage;
        }

        function savePdf() {
          const element = document.getElementById("inv-content");
          var filename = "<?php echo 'Delivery Note_'.sprintf('%06d', $dnote->note_no).'_'.$dnote->created_at; ?>";
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