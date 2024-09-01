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
            <form class="dashform row g-3" action="{{url('income-statement')}}" method="POST">
                @csrf
                <div class="col-md-6">
                    @if($settings->is_categorized)
                    <!-- <label class="control-label">{{trans('navmenu.category')}}</label> -->
                    <select name="category_id" id="category" class="form-control">
                        <option value="">{{trans('navmenu.all_categories')}}</option>
                        @if(!is_null($categories))
                        @foreach($categories as $key => $cat)
                        @if(!is_null($category) && $category->id === $cat->id)
                        <option selected value="{{$cat->id}}">{{$cat->name}}</option>
                        @else
                        <option value="{{$cat->id}}">{{$cat->name}}</option>
                        @endif
                        @endforeach
                        @endif
                    </select>
                    @endif
                </div>
                <input type="hidden" name="start_date" id="start_input" value="">
                <input type="hidden" name="end_date" id="end_input" value="">
                <!-- Date and time range -->
                <div class="col-md-6 float-end">
                    <div class="input-group">
                        <button type="button" class="btn btn-white pull-right" id="reportrange">
                            <span><i class="bx bx-calendar"></i></span>
                            <i class="bx bx-caret-down"></i>
                        </button>
                    </div>
                </div>
            </form>
            <div class="card">
                <div class="card-body">
                    <div id="inv-content">
                        <div class="col-md-12" style="text-align: center; text-transform: uppercase; color: blue; border-bottom: 3px sold red;">
                            @if(!is_null($shop->logo_location))
                            <figure>
                                <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                            </figure>
                            @endif
                            <h5>{{$shop->name}}</h5>
                            <h6>{{trans('navmenu.income_stmt')}}<br><br> <b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></h6>
                        </div>
                        <div class="col-md-12" style="border-top: 2px solid #82B1FF; padding: 35px;">
                            <h6 class="mb-3 text-uppercase text-center"><b>{{trans('navmenu.revenue')}}</b></h6>
                            <ul class="list-group list-group-unbordered">
                                <li class="list-group-item">
                                  {{trans('navmenu.sales')}} <span class="float-end">{{number_format($total_sales, 2, '.', ',')}}</span>
                                </li>
                                <li class="list-group-item">
                                  {{trans('navmenu.cost_of_sales')}} <span class="float-end">{{number_format($total_co_sales, 2, '.', ',')}}</span>
                                </li>
                                <li class="list-group-item" style="border-top: 2px solid #BDBDBD; border-bottom: 2px solid #BDBDBD;">
                                  <b>{{trans('navmenu.gross_profit')}} <span class="float-end">{{number_format($total_sales-$total_co_sales, 2, '.', ',')}}</span></b>
                                </li>
                            </ul>
                            <h6 class="mb-3 text-uppercase text-center pt-3"><b>{{trans('navmenu.expenses')}}</b></h6>
                            <ul class="list-group list-group-unbordered">
                                @foreach($expenses as $expense)
                                <li class="list-group-item">
                                    {{$expense['expense_type']}} <span class="float-end">
                                    {{number_format($expense['amount'], 2, '.', ',')}}</span>
                                </li>
                                @endforeach
                                <li class="list-group-item" style="border-top: 2px solid #BDBDBD; border-bottom: 2px solid #BDBDBD;"><b>{{trans('navmenu.total_expenses')}} <span class="float-end">{{number_format($totalexpenses, 2, '.', ',')}}</span></b></li>
                            </ul>
                            <h6 class="mb-3 text-uppercase text-center pt-3"><b>{{trans('navmenu.net_profit')}}</b></h6>
                            <ul class="list-group list-group-unbordered">
                                <li class="list-group-item" style="border-top: 2px solid #BDBDBD; border-bottom: 2px solid #BDBDBD;">
                                    <b>{{trans('navmenu.profit')}} <span class="float-end">{{number_format(($total_sales-$total_co_sales)-$totalexpenses, 2, '.', ',')}}</span></b>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3 pt-2 options" style="margin-top: 5px;">
                        <a href="#" onclick="javascript:printDiv('inv-content')" class="btn btn bg-info btn-sm float-end" style="margin-left: 5px;"><i class="bx bx-printer"></i> {{trans('navmenu.print')}}</a>
                        <a href="#" onclick="javascript:savePdf()" class="btn bg-warning btn-sm  float-end"><i class="bx bx-download"></i> Download PDF</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
<script type="text/javascript">
    function printDiv(divID) {
        //Get the HTML of div
        var divElements = document.getElementById(divID).innerHTML;
        //Get the HTML of whole page
        var oldPage = document.body.innerHTML;
        //Reset the page's HTML with div's HTML only
        document.body.innerHTML = divElements;
        //File name for printed ducument
        document.title = "<?php echo trans('navmenu.income_stmt').'_'.$duration; ?>";
        //Print Page
        window.print();
        //Restore orignal HTML
        document.body.innerHTML = oldPage;
    }

    function savePdf() {
        const element = document.getElementById("inv-content");
        var filename = "<?php echo trans('navmenu.income_stmt').'_'.$reporttime; ?>";
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