<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>{{$shop->display_name}}  | Payment_vochar | {{$recno}}</title>
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="../css/receipt.css">
</head>
<body>
  <div id="invoice-POS">
    <center id="top">
      <div class="logo">
        @if(!is_null($shop->logo_location))
        <figure>
          <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="" style="width: 60px; height: 60px">
        </figure>
        @endif
      </div>
      <div class="info"> 
        <h3>{{$shop->name}}</h3>
      </div><!--End Info-->
    </center><!--End InvoiceTop-->
    
    <div id="mid" style="text-align: center;">
      <div class="info">
        <!-- <h2>Contact Info</h2> -->
        <p> 
            {{trans('navmenu.address')}} : {{$shop->street}}, {{$shop->district}},{{$shop->city}}</br>
            {{trans('navmenu.email')}}   : {{$shop->email}}</br>
            {{trans('navmenu.mobile')}}   : {{$shop->mobile}}</br>
        </p>
      </div>
      <h5 style="text-transform: uppercase;">{{trans('navmenu.payment_voucher')}}: {{$recno}}</h5>
      <div>
        <span>{{trans('navmenu.date')}}: <strong>{{$date}}</strong></span>
      </div>
    </div><!--End Invoice Mid-->
    
    <div id="bot">
          <div id="table">
            <table class="table" width="100%">
              <thead>
                <th><h6>{{trans('navmenu.description')}}</h6></th>
                <th><h6>{{trans('navmenu.amount')}}</h6></th>
              </thead>
              <tbody>
                <tr>
                  <td><b>{{$expense->expense_type}}</b> : {{$expense->description}}</td>
                  <td>{{number_format($expense->amount)}}</td>
                </tr>
                <tr class="amount-total">
                  <th style="text-align: right;">{{trans('navmenu.total')}}</th>
                  <td>{{number_format($expense->amount-$expense->expense_discount)}}</td>
                </tr>
              </tbody>
            </table>
        

          </div><!--End Table-->
        <div style="text-align: center;">

          <span>_ _ _ _ _ _ _ _ _ <br>
          {{trans('navmenu.authorized_by')}}</span><br>

          <span>_ _ _ _ _ _ _ _ _ <br>
          {{trans('navmenu.received_by')}}</span>
    
        </div>
        </div><!--End InvoiceBot-->
      </div><!--End Invoice-->

      <div class="modal-footer">
        <!-- <button id="btnDownload" type="button" class="btn btn-success"> -->
          <!-- <span class="glyphicon glyphicon-file"></span> DOWNLOAD</button> -->
        <button id="btnPrint" type="button" class="btn btn-primary">
          <span class="bx bx-printer"></span> {{trans('navmenu.print')}}</button>
          <!-- <button class="btn btn-success">
          <span class="glyphicon glyphicon-envelope"></span> EMAIL</button> -->
          <a href="{{url('expenses')}}" onclick="closeFunction()"  class="btn btn-warning">
          <span class='bx bx-remove'></span>{{trans('navmenu.btn_cancel')}}</a>                
      </div>

      <script
        src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js" integrity="sha384-NaWTHo/8YCBYJ59830LTz/P4aQZK1sS0SneOgAvhsIl3zBu8r9RevNg5lHCHAuQ/" crossorigin="anonymous"></script>
      <script>
        
        $(document).ready( function ()  {
            $('#btnPrint').on("click", function(e) {

                if ($("#printer").length) {
                    $("#printer").remove();
                }

                var divElements = $("#invoice-POS").html();
                var iframe = $('<iframe class="hidden" id="printer"></iframe>').appendTo('body');
                var printer = $('#printer');
                printer.contents().find('body').append('<!DOCTYPE html><head><title>Print Title</title><link rel="stylesheet" href="../bower_components/bootstrap/dist/css/bootstrap.min.css"></head><body>' + divElements + '</body>');
                setTimeout(function() {  
                    printer.get(0).contentWindow.print();

                }, 250);
            });
        });
      </script>
</body>
</html>
  
