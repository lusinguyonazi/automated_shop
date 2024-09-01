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
            <form class="row g-3 dashform" action="{{url('expenses-report')}}" method="POST">
                @csrf
                <div class="col-md-3">
                    <select name="expense" onchange="this.form.submit()" class="form-control select2">
                        @if(!is_null($expense1))
                        <option>{{$expense1->expense_type}}</option>
                        <option value="">{{trans('navmenu.select_by_expense_type')}}</option>
                        @else
                        <option value="">{{trans('navmenu.select_by_expense_type')}} </option>
                        @endif
                        @foreach($exptypes as $expense)
                        <option>{{$expense->expense_type}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="expense_category_id" onchange="this.form.submit()" class="form-select form-select-sm mb-3">
                        <option value="">---Select Expense Category ---</option>
                        @foreach($expcategories as $cat)
                        @if(!is_null($expcat) && $expcat->id == $cat->id)
                        <option value="{{$cat->id}}" selected>{{$cat->name}}</option>
                        @else
                        <option value="{{$cat->id}}">{{$cat->name}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="start_date" id="start_input" value="{{$start_date}}">
                <input type="hidden" name="end_date" id="end_input" value="{{$end_date}}">
                <!-- Date and time range -->
                <div class="col-md-6">  
                    <div class="form-group">
                        <div class="input-group">
                            <button type="button" class="btn btn-white btn-sm pull-right" id="reportrange">
                                <span><i class="fa fa-calendar"></i></span>
                                <i class="fa fa-caret-down"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-success" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#total-expenses-pdf" role="tab" aria-selected="true">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-list-minus font-18 me-1'></i></div>
                                    <div class="tab-title">{{trans('navmenu.total_expense_report')}} (PDF)</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#total-expenses" role="tab" aria-selected="true">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-list-minus font-18 me-1'></i></div>
                                    <div class="tab-title">{{trans('navmenu.total_expense_report')}} (Excel)</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#expenses-report" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-list-plus font-18 me-1'></i></div>
                                    <div class="tab-title">{{trans('navmenu.expense_report')}}</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="total-expenses-pdf" role="tabpanel">
                            <div id="inv-content">
                                <div class="row">
                                    <div class="col-xs-12" style="text-align: center; text-transform: uppercase; color: blue">
                                        @if(!is_null($shop->logo_location))
                                        <figure>
                                            <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                                        </figure>
                                        @endif  
                                        <h5>{{ $shop->name }}</h5>
                                        <h6>{{trans('navmenu.total_expense_report')}} <br><b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></h6>
                                    </div>
                                    <div class="col-xs-12">
                                        <p class="lead" style="text-transform: uppercase; color: #f44336; text-align: center;">{{trans('navmenu.category')}} : @if(!is_null($expcat)){{$expcat->name}}@else {{trans('navmenu.all')}}@endif</p>
                                        <div class="invoice-content">
                                            <table border="0" cellspacing="0" cellpadding="0">
                                                <thead>
                                                    <tr>
                                                        <th style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.expense_type')}}</th>
                                                        <th style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{trans('navmenu.amount')}}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $totalexpenses = 0; ?>
                                                    @foreach($texpenses as $index => $expense)
                                                    <?php $totalexpenses += $expense->amount; ?>
                                                    <tr>
                                                        <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">{{$expense->expense_type}}</td>
                                                        <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{number_format($expense->amount, 2, '.', ',')}}</td>
                                                    </tr>
                                                    @endforeach
                                                    <tr style="border-top: 2px solid #BDBDBD; border-bottom: 2px solid #BDBDBD;">
                                                        <td style="text-align: left; border-bottom: 1px solid #e0e0e0;"><b>{{trans('navmenu.total_expenses')}} ({{$defcurr->code}})</b></td>
                                                        <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{number_format($totalexpenses, 2, '.', ',')}}</b></td>
                                                    </tr>
                                                    @if($qty_produced > 0)
                                                    <tr>
                                                        <td style="text-align: left; border-bottom: 1px solid #e0e0e0;">Total Quantity Produced</td>
                                                        <td style="text-align: right; border-bottom: 1px solid #e0e0e0;">{{$qty_produced}}</td>
                                                    </tr>
                                                    <tr style=" border-bottom: 2px solid #BDBDBD;">
                                                        <td style="text-align: left; border-bottom: 1px solid #e0e0e0;"><b>Cost of Production per Unit</b></td>
                                                        <td style="text-align: right; border-bottom: 1px solid #e0e0e0;"><b>{{number_format(($totalexpenses/$qty_produced), 2, '.', ',')}}</b></td>
                                                    </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="margin-top: 10px;">
                                <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm" style="margin: 5px;"><i class="bx bx-download"></i> Download PDF</a>
                                <a href="#" onclick="javascript:printDiv('inv-content')" class="btn btn-secondary btn-sm pull-right" style="margin: 5px;"><i class="bx bx-printer"></i> Print</a>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="total-expenses" role="tabpanel">
                            <div class="col-xs-12" style="text-align: center; text-transform: uppercase; color: blue; border-bottom: 2px solid #82B1FF; margin-bottom: 8px;">
                                @if(!is_null($shop->logo_location))
                                <figure>
                                    <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                                </figure>
                                @endif
                                <h5>{{ $shop->name }}</h5>
                                <h6>{{trans('navmenu.total_expense_report')}} <br><b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></h6>
                            </div>
                            <div class="col-xs-12 table-responsive">
                                <table id="texpenses" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                    <thead style="background:#E0E0E0;">
                                        <tr>
                                            <th>{{trans('navmenu.expense_type')}}</th>
                                            <th>{{trans('navmenu.amount')}}</th>  
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($texpenses as $index => $expense)
                                        <tr>
                                            <td><a href="{{url('single-expense-report/'.$expense->expense_type)}}">{{$expense->expense_type}}</a></td>
                                            <td>{{number_format($expense->amount)}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th><b>{{trans('navmenu.total')}}</b></th>
                                            <th><b>{{number_format($total)}}</b></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="expenses-report" role="tabpanel">
                            <div class="col-xs-12" style="text-align: center; text-transform: uppercase; color: blue; border-bottom: 2px solid #82B1FF; margin-bottom: 8px;">
                                @if(!is_null($shop->logo_location))
                                <figure>
                                    <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                                </figure>
                                @endif
                                <h5>{{ $shop->name }}</h5>
                                <h6 class="title">{{trans('navmenu.expense_report')}} <br><b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></h6>
                            </div>
                            <div class="col-xs-12 table-responsive">
                                <table id="expenses" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                    <thead style="background:#E0E0E0;">
                                        <tr>
                                            <th>#</th>
                                            <th>{{trans('navmenu.expense_date')}}</th>
                                            <th>{{trans('navmenu.supplier_name')}}</th>
                                            <th>{{trans('navmenu.expense_type')}}</th>
                                            <th>{{trans('navmenu.description')}}</th>
                                            <th>{{trans('navmenu.amount')}}</th>
                                            @if($settings->is_vat_registered)
                                            <th>VAT</th>
                                            @endif
                                            @if($settings->estimate_withholding_tax)
                                            <th>{{trans('navmenu.wht_rate')}}</th>
                                            <th>{{trans('navmenu.wht_amount')}}</th>
                                            @endif   
                                            @if($shop->subscription_type_id >= 3)
                                            <th>{{trans('navmenu.exp_type')}}</th>
                                            <th>{{trans('navmenu.status')}}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($expenses as $index => $expense)
                                        <tr>
                                            <td>{{$index+1}}</td>
                                            <td>{{date("d, M Y", strtotime($expense->created_at))}}</td>
                                            @if(!is_null($expense->supplier_id))
                                            <td>{{App\Models\Supplier::find($expense->supplier_id)->name}}</td>
                                            @else
                                            <td>{{trans('navmenu.unknown')}}</td>
                                            @endif
                                            <td>{{$expense->expense_type}}</td>
                                            <td>{{$expense->description}}</td>
                                            <td>{{number_format($expense->amount)}}</td>
                                            @if($settings->is_vat_registered)
                                            <td>{{number_format($expense->exp_vat)}}</td>
                                            @endif
                                            @if($settings->estimate_withholding_tax)
                                            <td style="text-align: center;">{{number_format($expense->wht_rate)}} </td>
                                            <td style="text-align: center;">{{number_format($expense->wht_amount)}} </td>
                                            @endif
                                            @if($shop->subscription_type_id >= 3)
                                            <td>{{$expense->exp_type}}</td>
                                            <td>{{$expense->status}}</td>
                                            @endif
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th><b>{{trans('navmenu.total')}}</b></th>
                                            <th><b>{{number_format($total)}}</b></th>
                                            @if($settings->is_vat_registered)
                                            <th>{{$total_vat}}</th>
                                            @endif
                                            @if($settings->estimate_withholding_tax)
                                            <th></th>
                                            <th><b>{{$total_wht}}</b></th>
                                            @endif
                                            @if($shop->subscription_type_id >= 3)
                                            <th></th>
                                            <th></th>
                                            @endif
                                        </tr>
                                    </tfoot>
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
    <script language="javascript" type="text/javascript">
        function printDiv(divID) {
            //Get the HTML of div
            var divElements = document.getElementById(divID).innerHTML;
            //Get the HTML of whole page
            var oldPage = document.body.innerHTML;
            //Reset the page's HTML with div's HTML only
            document.body.innerHTML = divElements;
            //File name for printed ducument
            document.title = "<?php echo trans('navmenu.debtor_account_stmt').'_'.$reporttime; ?>";
            //Print Page
            window.print();
            //Restore orignal HTML
            document.body.innerHTML = oldPage;
        }

        function savePdf() {
            const element = document.getElementById("inv-content");
            var filename = "<?php echo trans('navmenu.debtor_account_stmt').'_'.$reporttime; ?>";
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