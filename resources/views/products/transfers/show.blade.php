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
        <div class="col-md-9 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
            <hr/>
            <div class="card">
                <div class="card-header">
                    <div class="btn-group float-end">
                        <a href="{{route('transfer-orders.edit', encrypt($transorder->id))}}" class="btn bg-info " style="margin: 5px;"><i class="fa fa-edit" ></i> Edit</a>
                        <a href="#" onclick="javascript:savePdf()" class="btn bg-warning" style="margin: 5px;"><i class="bx bx-download"></i> Download PDF</a>
                        <a href="#" onclick="javascript:printDiv('inv-content')" class="btn bg-success" style="margin: 5px;"><i class="bx bx-printer"></i> {{trans('navmenu.print')}}</a>
                    </div>
                </div>
                <div class="card-body">
                    <div id="inv-content">
                        <div class="clearfix invoice-header">
                            <div class="title text-center" style="margin-bottom: 5px;">
                                <h3 class="mb-0 text-uppercase text-center">{{$title}}</h3><br>
                                <h6>{{trans('navmenu.transfer_date')}} : <b>{{date("d, M Y", strtotime($transorder->order_date))}}</b></h6>
                            </div>
                            @if(!is_null($source->logo_location))
                            <figure>
                                <img class="invoice-logo" src="{{asset('storage/logos/'.$source->logo_location)}}" alt="">
                            </figure>
                            @endif
                            <div class="company-address">
                                <p style="text-transform: uppercase;">
                                    {{trans('navmenu.source_shop')}}: <br> <span class="font-15" style="color: blue;">{{$source->name}}</span>
                                </p>
                            </div>
                            <div class="company-contact">
                                <p style="text-transform: uppercase; font-size: 14px;">
                                    {{trans('navmenu.destin_shop')}}:<br> <span class="font-15" style="color: blue;">{{$destin->name}}</span>
                                </p>
                                    
                            </div>
                        </div>
                        

                        <div class="invoice-content">
                            <div class="invoice-details clearfix">
                                <div class="invoice-reason text-center">
                                    <p><strong class="font-18">{{trans('navmenu.reason')}}:</strong> {{$transorder->reason}}</p>
                                </div>
                            </div>

                            <div class="col-md-12 order-items pt-3">
                                <h6 class="mb-3 text-uppercase text-center">{{trans('navmenu.transfer_items')}}</h6>
                                <table border="0" cellspacing="0" cellpadding="0">
                                    <thead>
                                        <tr style="font-size: 12px;">
                                            <th class="Item" style="text-align: left;">{{trans('navmenu.item_name')}}</th>
                                            <th class="source">{{trans('navmenu.source_stock')}}</th>
                                            <th class="destin">{{trans('navmenu.destin_stock')}}</th>
                                            <th class="qty">{{trans('navmenu.transfer_qty')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($orderitems as $key => $item)
                                        <tr>
                                            <td class="item"><h6>{{$item->product->name}}</h6></td>
                                            <td class="source">{{number_format($item->source_stock)}}</td>
                                            <td class="destin">{{number_format($item->destin_stock)}}</td>
                                            <td class="qty">{{number_format($item->quantity)}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="clearfix order-bottom " style="margin-top: 15px;">
                            <div class="issuer" style="width: 50%; float: left; padding-left: 55px;">
                                <p>
                                    <span style="text-transform: uppercase; font-size: 18px; font-weight: bold;">{{trans('navmenu.transfer_by')}}</span><br>
                                    {{trans('navmenu.name')}} : <strong>{{$user->first_name}} {{$user->last_name}}</strong><br>
                                    {{trans('navmenu.signature')}} <strong>.....................</strong>
                                </p>                        
                            </div>
                            <div class="receiver" style="width: 50%; float: right; padding-left: 55px;">
                                <p>
                                    <span style="text-transform: uppercase; font-size: 18px; font-weight: bold;">{{trans('navmenu.stock_received_by')}}</span><br>
                                    {{trans('navmenu.name')}} : <strong>.........................</strong><br>
                                    {{trans('navmenu.signature')}} <strong>.....................</strong>
                                </p>
                            </div>
                        </div>
                    </div>
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
            document.title = "<?php echo trans('navmenu.title').'_'.$transorder->created_at; ?>";
            
            //Print Page
            window.print();

            //Restore orignal HTML
            document.body.innerHTML = oldPage;

        }

        function savePdf() {
          const element = document.getElementById("inv-content");
          var filename = "<?php echo trans('navmenu.title').'_'.$transorder->created_at; ?>";
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