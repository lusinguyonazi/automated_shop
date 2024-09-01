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
        <div class="col-md-10 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
            <hr/>
            <div class="card">
                <div class="card-header">
                    <div class="options">
                        <!-- this row will not appear when printing -->
                        <a href="#" onclick="javascript:printDivBS('print-bs')" class="btn btn-info btn-sm float-end"><i class="bx bx-printer"></i> {{trans('navmenu.print')}}</a>
                        <a href="#" onclick="javascript:saveBSPdf()" class="btn btn-warning btn-sm float-end" style="margin-right: 5px;"><i class="bx bx-download"></i> Download PDF</a>
                    </div>
                </div>
                <div class="card-body" style="padding: 35px;">
                    <div id="print-bs">
                        <div class="col-md-12" style="text-align: center; text-transform: uppercase; color: blue">
                            @if(!is_null($shop->logo_location))
                            <figure>
                                <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                            </figure>
                            @endif
                            <h5>{{$shop->name}}</h5>
                            <h6>{{trans('navmenu.business_value')}}</h4>
                            <p>{{$reporttime}}</p></h6>
                        </div>
                        <div class="col-md-12" style="border-top: 2px solid #82B1FF;">
                            <h4>{{trans('navmenu.assets')}}</h4>
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td>{{trans('navmenu.total_cash')}}</td>
                                            <td style="text-align: right;">{{number_format($cash_in_hand, 2, '.', ',')}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{trans('navmenu.stock_value')}}</td>
                                            <td style="text-align: right;">{{number_format($inventory, 2, '.', ',')}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{trans('navmenu.cust_debts')}}</td>
                                            <td style="text-align: right;">{{number_format($account_receivable, 2, '.', ',')}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{trans('navmenu.supp_debts')}}</td>
                                            <td style="text-align: right;">
                                            {{number_format($supp_debtor, 2, '.', ',')}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{trans('navmenu.other_loan')}}</td>
                                            <td style="text-align: right;">
                                            {{number_format($other_loan, 2, '.', ',')}}</td>
                                        </tr>
                                        <tr style="border-top: 2px solid #BDBDBD; border-bottom: 2px solid #BDBDBD;">
                                            <td style="text-transform: uppercase;"><strong>{{trans('navmenu.total')}}</strong></td>
                                            <td style="text-align: right;"><strong>{{number_format($total_assets, 2, '.', ',')}}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <h4>{{trans('navmenu.credits')}}</h4>
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td>{{trans('navmenu.supp_credits')}}</td>
                                            <td style="text-align: right;">{{number_format($supplier_payable, 2, '.', ',')}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{trans('navmenu.cust_credits')}}</td>
                                            <td style="text-align: right;">{{number_format($cust_creditor, 2, '.', ',')}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{trans('navmenu.unpaid_expenses')}}</td>
                                            <td style="text-align: right;">{{number_format($unpaidexp, 2, '.', ',')}}</td>
                                        </tr>
                                        <tr>
                                            <td>{{trans('navmenu.other_credits')}}</td>
                                            <td style="text-align: right;">{{number_format($other_credits, 2, '.', ',')}}</td>
                                        </tr>
                                        <tr style="border-top: 2px solid #BDBDBD; border-bottom: 2px solid #BDBDBD;">
                                            <td style="text-transform: uppercase;"><strong>{{trans('navmenu.total_credits')}}</strong></td>
                                            <td style="text-align: right;"><strong>{{number_format($account_payable, 2, '.', ',')}}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <h4>{{trans('navmenu.business_value')}}</h4>
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr style="border-top: 2px solid #BDBDBD; border-bottom: 2px solid #BDBDBD;">
                                            <td style="text-transform: uppercase;"><strong>{{trans('navmenu.business_value')}}</strong></td>
                                            <td style="text-align: right;"><strong>{{number_format($total_assets-$account_payable, 2, '.', ',')}}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td><span>***</span>{{trans('navmenu.paid_expenses')}} (<b>{{date('F Y', strtotime($crtime))}}</b>)</td>
                                            <td style="text-align: right;">{{number_format($paid_expenses, 2, '.', ',')}}</td>
                                        </tr>
                                        <tr>
                                            <td><span>***</span>{{trans('navmenu.discounts_made')}} (<b>{{date('F Y', strtotime($crtime))}}</b>)</td>
                                            <td style="text-align: right;">{{number_format($discounts_made, 2, '.', ',')}}</td>
                                        </tr>
                                    </tbody>
                                </table>
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
<script type="text/javascript">
    function printDivBS(divID) {
        //Get the HTML of div
        var divElements = document.getElementById(divID).innerHTML;
        //Get the HTML of whole page
        var oldPage = document.body.innerHTML;
        //Reset the page's HTML with div's HTML only
        document.body.innerHTML = divElements;
        //File name for printed ducument
        document.title = "<?php echo trans('navmenu.business_value').'_'.$duration; ?>";
        //Print Page
        window.print();
        //Restore orignal HTML
        document.body.innerHTML = oldPage;
    }

    function saveBSPdf() {
        const element = document.getElementById("print-bs");
        var filename = "<?php echo trans('navmenu.business_value').'_'.$reporttime; ?>";
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