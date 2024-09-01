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
            <form class="dashform row g-3" action="{{url('collections-report')}}" method="POST">
                @csrf
                <div class="col-md-6">
                    <select name="customer_id" class="form-control select2">
                        @if(!is_null($customer))
                        <option value="{{$customer->id}}">{{$customer->name}}</option>
                        <option value="">{{trans('navmenu.select_by_customer')}}</option>
                        @else
                        <option value="">{{trans('navmenu.select_by_customer')}} </option>
                        @endif
                        @foreach($customers as $cust)
                        <option value="{{$cust->id}}">{{$cust->name}}</option>
                        @endforeach
                    </select>
                </div>
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
                    <ul class="nav nav-tabs nav-success" role="tablist">    
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="tab" href="#all-collections" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-export font-18 me-1'></i></div>
                                    <div class="tab-title">{{trans('navmenu.collections_report')}}</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="tab" href="#debts-collect" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-export font-18 me-1'></i></div>
                                    <div class="tab-title">{{trans('navmenu.debt_collections_report')}}</div>
                                </div>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content py-3">
                        <div class="tab-pane fade show active" id="all-collections" role="tabpanel">
                            <div class="col-xs-12" style="text-align: center; text-transform: uppercase; color: blue; border-bottom: 2px solid #82B1FF; margin-bottom: 8px;">
                                @if(!is_null($shop->logo_location))
                                <figure>
                                    <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                                </figure>
                                @endif
                                <h5>{{$shop->name}}</h5>
                                <h6>{{trans('navmenu.collections_report')}} <br><b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></h6>
                            </div>
                            <div class="col-xs-12 table-responsive">
                                <table id="collections-report" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.customer_id')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.customer_name')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.payment_mode')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.cheque_no')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.bank_name')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.date_of_pay')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.receipt_no')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.sale_type')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.amount')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $total = 0; ?>
                                        @foreach($collections as $payment)
                                        <?php  $total += $payment->amount; ?>
                                        <tr>
                                            <td style="text-align: center;">{{sprintf('%03d', $payment->cust_no)}}</td>
                                            <td style="text-align: center;">{{$payment->name}}</td>
                                            <td style="text-align: center;">{{$payment->pay_mode}}</td>
                                            <td style="text-align: center;">{{$payment->cheque_no}}</td>
                                            <td style="text-align: center;">{{$payment->bank_name}}</td>
                                            <td style="text-align: center;">{{$payment->pay_date}}</td>
                                            <td style="text-align: center;">{{sprintf('%05d', $payment->receipt_no)}}</td>
                                            <td style="text-align: center;">{{$payment->sale_type}}</td>
                                            <td style="text-align: center;">{{number_format($payment->amount, 2, '.', ',')}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th style="text-align: right; text-transform: uppercase;">{{trans('navmenu.total')}}</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th style="text-align: center;">{{number_format($total, 2, '.', ',')}}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="debts-collect" role="tabpanel">
                            <div class="col-xs-12" style="text-align: center; text-transform: uppercase; color: blue; border-bottom: 2px solid #82B1FF; margin-bottom: 8px;">
                                @if(!is_null($shop->logo_location))
                                <figure>
                                    <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                                </figure>
                                @endif
                                <h5>{{$shop->name}}</h5>
                                <h6>{{trans('navmenu.debt_collections_report')}} <br><b>@if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</b></h6>
                            </div>
                            <div class="col-xs-12 table-responsive">
                                <table id="debt-collections-report" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.customer_id')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.customer_name')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.payment_mode')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.cheque_no')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.bank_name')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.date_of_pay')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.receipt_no')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.sale_type')}}</th>
                                            <th style="text-align: center; text-transform: uppercase;">{{trans('navmenu.amount')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $total = 0; ?>
                                        @foreach($debt_collections as $payment)
                                        <?php $total += $payment->amount; ?>
                                        <tr>
                                            <td style="text-align: center;">{{sprintf('%03d', $payment->cust_id)}}</td>
                                            <td style="text-align: center;">{{$payment->name}}</td>
                                            <td style="text-align: center;">{{$payment->pay_mode}}</td>
                                            <td style="text-align: center;">{{$payment->cheque_no}}</td>
                                            <td style="text-align: center;">{{$payment->bank_name}}</td>
                                            <td style="text-align: center;">{{$payment->pay_date}}</td>
                                            <td style="text-align: center;">{{sprintf('%05d', $payment->receipt_no)}}</td>
                                            <td style="text-align: center;">{{$payment->sale_type}}</td>
                                            <td style="text-align: center;">{{number_format($payment->amount, 2, '.', ',')}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th style="text-align: right; text-transform: uppercase;">{{trans('navmenu.total')}}</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th style="text-align: center;">{{number_format($total, 2, '.', ',')}}</th>
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