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
            <form class="dashform row g-3" action="{{url('closing-business-value')}}" method="POST">
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
                        <div class="col-md-12" style="text-align: center; text-transform: uppercase; color: blue">
                            @if(!is_null($shop->logo_location))
                            <figure>
                                <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                            </figure>
                            @endif
                            <h5>{{$shop->name}}</h5>
                            <h6 class="title">{{trans('navmenu.monthly_value')}}</h6>
                            <p>{{$reporttime}}</p>
                        </div>
                        <div style="border-top: 2px solid #82B1FF; padding: 5px;" class="col-md-12 invoice-content">
                            <table border="0" cellspacing="0" cellpadding="0">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">{{trans('navmenu.date')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.assets')}} / {{trans('navmenu.credits')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.business_value')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.paid_expenses')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.discounts_made')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($bvalues as $value)
                                    <tr>
                                        <td style="text-align: center; border-bottom: 2px solid #e0e0e0;">{{date('M d, Y', strtotime($value->date))}}</td>
                                        <td style="text-align: center; border-bottom: 2px solid #e0e0e0;">
                                            <table>
                                                <tbody>
                                                    <tr>
                                                      <th style="text-align: center; border: 1px solid #e0e0e0;">{{trans('navmenu.total_cash')}}</th>
                                                      <th style="text-align: center; border: 1px solid #e0e0e0;">{{trans('navmenu.stock_value')}}</th>
                                                      <th style="text-align: center; border: 1px solid #e0e0e0;">{{trans('navmenu.cust_debts')}}</th>
                                                      <th style="text-align: center; border: 1px solid #e0e0e0;">{{trans('navmenu.supp_debts')}}</th>
                                                      <th style="text-align: center; border: 1px solid #e0e0e0;">{{trans('navmenu.other_loan')}}</th>
                                                      <th style="text-align: center; border: 1px solid #e0e0e0;"><b>{{trans('navmenu.total')}}</b></th>
                                                    </tr>
                                                    <tr>
                                                      <td style="text-align: center; border: 1px solid #e0e0e0;">{{number_format($value->total_cash, 2, '.', ',')}}</td>
                                                      <td style="text-align: center; border: 1px solid #e0e0e0;">{{number_format($value->stock_value, 2, '.', ',')}}</td>
                                                      <td style="text-align: center; border: 1px solid #e0e0e0;">{{number_format($value->cust_debts, 2, '.', ',')}}</td>
                                                      <td style="text-align: center; border: 1px solid #e0e0e0;">{{number_format($value->supp_debts, 2, '.', ',')}}</td>
                                                      <td style="text-align: center; border: 1px solid #e0e0e0;">{{number_format($value->other_debts, 2, '.', ',')}}</td>
                                                      <td style="text-align: center; border: 1px solid #e0e0e0;"><b style="color: blue;">{{number_format(($value->total_cash+$value->cust_debts+$value->supp_debts+$value->other_debts), 2, '.', ',')}}</b></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table>
                                                <tbody>
                                                    <tr>
                                                      <th style="text-align: center; border: 1px solid #e0e0e0;">{{trans('navmenu.supp_credits')}}</th>
                                                      <th style="text-align: center; border: 1px solid #e0e0e0;">{{trans('navmenu.cust_credits')}}</th>
                                                      <th style="text-align: center; border: 1px solid #e0e0e0;">{{trans('navmenu.unpaid_expenses')}}</th>
                                                      <th style="text-align: center; border: 1px solid #e0e0e0;">{{trans('navmenu.other_credits')}}</th>
                                                      <th style="text-align: center; border: 1px solid #e0e0e0;"><b>{{trans('navmenu.total_credits')}}</b></th>
                                                    </tr>
                                                    <tr>    
                                                      <td style="text-align: center; border: 1px solid #e0e0e0;">{{number_format($value->supp_credits, 2, '.', ',')}}</td>
                                                      <td style="text-align: center; border: 1px solid #e0e0e0;">{{number_format($value->cust_credits, 2, '.', ',')}}</td>
                                                      <td style="text-align: center; border: 1px solid #e0e0e0;">{{number_format($value->unpaid_expenses, 2, '.', ',')}}</td>
                                                      <td style="text-align: center; border: 1px solid #e0e0e0;">{{number_format($value->other_credits, 2, '.', ',')}}</td>
                                                      <td style="text-align: center; border: 1px solid #e0e0e0;"><b style="color: red;">{{number_format(($value->supp_credits+$value->cust_credits+$value->unpaid_expenses+$value->other_credits), 2, '.', ',')}}</b></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td style="text-align: center; border-bottom: 2px solid #e0e0e0;"><b style="color: green;">{{number_format(($value->total_cash+$value->cust_debts+$value->supp_debts+$value->other_debts)-($value->supp_credits+$value->cust_credits+$value->unpaid_expenses+$value->other_credits), 2, '.', ',')}}</b></td>
                                        <td style="text-align: center; border-bottom: 2px solid #e0e0e0;">{{number_format($value->paid_expenses, 2, '.', ',')}}</td>
                                        <td style="text-align: center; border-bottom: 2px solid #e0e0e0;">{{number_format($value->discounts_made, 2, '.', ',')}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
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