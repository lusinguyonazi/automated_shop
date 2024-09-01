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
            <form class="dashform row g-3" action="{{url('daily-cash-flow-statement')}}" method="POST">
                @csrf
                <div class="col-md-6"></div>
                <input type="hidden" name="start_date" id="start_input" value="">
                <input type="hidden" name="end_date" id="end_input" value="">
                <!-- Date and time range -->
                <div class="col-md-6 mb-3">
                    <div class="input-group">
                        <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange">
                            <span><i class="bx bx-calendar"></i></span>
                            <i class="bx bx-caret-down"></i>
                        </button>
                    </div>
                </div>
            </form>
            <div class="card">
                <div class="card-body">
                    <div id="inv-content">
                        <div  style="text-align: center; text-transform: uppercase; color: blue; border-bottom: 3px sold red;">
                            @if(!is_null($shop->logo_location))
                            <figure>
                                <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                            </figure>
                            @endif
                            <h5>{{$shop->name}}</h5>
                            <h6>{{trans('navmenu.daily_cashflow_stmt')}}<br><br> <b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}} @endif</b></h6>
                        </div>

                        <div class="invoice-content">
                            <h6 class="profile-username text-center"><b>{{trans('navmenu.transactions')}}</b></h6>
                            <table border="0" cellspacing="0" cellpadding="0">
                                <thead>
                                    <tr>
                                        <th style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.transaction')}}</th>
                                        <th style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.amount')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.cash_payments')}}</td>
                                        <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($cash_pay, 2, '.', ',')}}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.mob_payments')}}</td>
                                        <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($mob_pay, 2, '.', ',')}}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.bank_payments')}}</td>
                                        <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($bank_pay, 2, '.', ',')}}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left; border-bottom: 1px solid #e0e0e0;"><b>{{trans('navmenu.total_amount')}}</b></td>
                                        <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{number_format($total_pay, 2, '.', ',')}}</b></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.cash_to_bank')}}</td>
                                        <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($c_to_b, 2, '.', ',')}}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.cash_out')}}</td>
                                        <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($cash_out, 2, '.', ',')}}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.paid_expenses')}}</td>
                                        <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($paid_expenses, 2, '.', ',')}}</td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: left; border-bottom: 1px solid #e0e0e0;"><b>{{trans('navmenu.closing_balance')}}</b></td>
                                        <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{number_format($cashBal, 2, '.', ',')}}</b></td>
                                    </tr>
                                </tbody>
                            </table>
                            <h6 class="profile-username text-center"><b>{{trans('navmenu.expenses')}}</b></h6>
                            <table border="0" cellspacing="0" cellpadding="0">
                                <thead>
                                    <tr>
                                        <th style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.expense_type')}}</th>
                                        <th style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.amount')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $totalexp = 0?>
                                    @foreach($dexpenses as $expense)
                                    <tr>
                                        <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{$expense->expense_type}}</td>
                                        <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($expense->amount, 2, '.', ',')}}</td>
                                    </tr>
                                    <?php $totalexp += $expense->amount ?>
                                    @endforeach
                                    
                                    <tr style="border-top: 2px solid #BDBDBD; border-bottom: 2px solid #BDBDBD;">
                                        <td style="text-align: left; border-bottom: 1px solid #e0e0e0;"><b>{{trans('navmenu.total_expenses')}}</b></td>
                                        <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{number_format($totalexp, 2, '.', ',')}}</b></td>
                                    </tr>
                                </tbody>
                            </table>
                            <h6 class="profile-username text-center"><b>{{trans('navmenu.cash_out')}}</b></h6>
                            <table border="0" cellspacing="0" cellpadding="0">
                                <thead>
                                    <tr>
                                        <th style="text-align:left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.reason')}}</th>
                                        <th style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.amount')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($couts as $cout)
                                    <tr>
                                        <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{$cout->reason}}</td>
                                        <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($cout->amount, 2, '.', ',')}}</td>
                                    </tr>
                                    @endforeach
                                    <tr style="border-top: 2px solid #BDBDBD; border-bottom: 2px solid #BDBDBD;">
                                        <td style="text-align: left; border-bottom: 1px solid #e0e0e0;"><b>{{trans('navmenu.total')}}</b></td>
                                        <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{number_format($cash_out, 2, '.', ',')}}</b></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3 pt-2 options" style="margin-top: 5px;">
                        <a href="#" onclick="javascript:printDivDCStmt('inv-content')" class="btn btn bg-info btn-sm float-end" style="margin-left: 5px;"><i class="bx bx-printer"></i> {{trans('navmenu.print')}}</a>
                        <a href="#" onclick="javascript:saveDCSPdf()" class="btn bg-warning btn-sm  float-end"><i class="bx bx-download"></i> Download PDF</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
<script type="text/javascript">
    function printDivDCStmt(divID) {
        //Get the HTML of div
        var divElements = document.getElementById(divID).innerHTML;
        //Get the HTML of whole page
        var oldPage = document.body.innerHTML;
        //Reset the page's HTML with div's HTML only
        document.body.innerHTML = divElements;
        //File name for printed ducument
        document.title = "<?php echo trans('navmenu.daily_cashflow_stmt').'_'.$duration; ?>";
        //Print Page
        window.print();
        //Restore orignal HTML
        document.body.innerHTML = oldPage;
    }

    function saveDCSPdf() {
        const element = document.getElementById("inv-content");
        var filename = "<?php echo trans('navmenu.daily_cashflow_stmt').'_'.$reporttime; ?>";
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