@extends('layouts.app')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('admin/home')}}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">

        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row row-cols-1 row-cols-md-1 row-cols-lg-1 row-cols-xl-1">
        <div class="col">
            <h6 class="mb-0 text-uppercase">{{$title}}</h6>
            <hr/>
            <div class="card">
                <div class="card-body">
                    <form class="form" method="POST" action="{{ route('sale-payments.update', $payment->id)}}">
                        @csrf
                        {{ method_field('PATCH') }} 
                        <div class="row g-3">
                            
                            <div class="form-group col-md-4">
                                <label class="form-label">{{trans('navmenu.pay_date')}}</label>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" name="pay_date" id="pay_date" placeholder="Choose date payment" class="form-control" required value="{{$payment->pay_date}}">
                                    
                                </div>
                            </div>

                            <div class="form-group col-md-4">
                                <label class="form-label">{{trans('navmenu.amount_paid')}} <span  style="color: red; font-weight: bold;">*</span></label>
                                <input id="name" type="number" step="any" name="amount" required placeholder="Please enter Amount Paid" class="form-control" value="{{$payment->amount}}">
                            </div>

                            <div class="form-group col-md-4">
                                <label class="form-label">{{trans('navmenu.pay_mode')}} <span  style="color: red; font-weight: bold;">*</span></label>
                                <select class="form-control" name="pay_mode" onchange="detailUpdate(this)" required>
                                    <option value="{{$payment->pay_mode}}">@if($payment->pay_mode == 'Cash')
                                        @if(app()->getLocale() == 'en')
                                        {{$payment->pay_mode}}
                                        @else
                                        {{trans('navmenu.cash')}}
                                        @endif
                                    @elseif($payment->pay_mode == 'Mobile Money')
                                        @if(app()->getLocale() == 'en')
                                        {{$payment->pay_mode}}
                                        @else
                                        {{trans('navmenu.mobilemoney')}}
                                        @endif
                                    @elseif($payment->pay_mode == 'Cheque')
                                        @if(app()->getLocale() == 'en')
                                        {{$payment->pay_mode}}
                                        @else
                                        {{trans('navmenu.cheque')}}
                                        @endif
                                    @elseif($payment->pay_mode == 'Bank')
                                        @if(app()->getLocale() == 'en')
                                        {{$payment->pay_mode}}
                                        @else
                                        {{trans('navmenu.bank')}}
                                        @endif      
                                    @endif</option>
                                    <option value="Cash">{{trans('navmenu.cash')}}</option>
                                    <option value="Cheque">{{trans('navmenu.cheque')}}</option>
                                    <option value="Bank">{{trans('navmenu.bank')}}</option>
                                    <option value="Mobile Money">{{trans('navmenu.mobilemoney')}}</option>
                                </select>
                            </div>
                            @if($shop->subscription_type_id ==2)
                        <div id="bankdetail" style="display: none;">
                            <div class="form-group col-md-4" id="deposit_mode" style="display: none;">
                                <label class="form-label">Deposit Mode</label>
                                <select name="deposit_mode" class="form-control">
                                    <option>Direct Deposit</option>
                                    <option>Bank Transfer</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label class="form-label">Bank Name </label>
                                <select name="bank_name" class="form-control">
                                    @foreach($bdetails as $detail)
                                    <option value="{{$detail->id}}">{{$detail->bank_name}} - {{$detail->branch_name}}</option>
                                    @endforeach
                                </select>                          
                            </div>

                            <div class="form-group col-md-4" id="cheque" style="display: none;">
                                <label class="form-label">Cheque Number</label>
                                <input id="name" type="text" name="cheque_no" placeholder="Please enter Cheque Number" class="form-control" value="{{$payment->cheque_no}}">
                            </div>

                            <div class="form-group col-md-4" id="expire" style="display: none;">
                                <label class="form-label">Expire Date</label>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div> 
                                    <input id="name" type="text" name="expire_date" placeholder="Please enter Expire Date" class="form-control" value="{{$payment->expire_date}}">
                                </div>
                            </div>

                            <div class="form-group col-md-6" id="slip" style="display: none;">
                                <label class="form-label">Credit Card/Bank Slip Number</label>
                                <input id="name" type="text" name="slip_no" placeholder="Please enter Credit Card/Bank Slip number" class="form-control" value="{{$payment->cheque_no}}">
                            </div>
                        </div>
                        <div id="mobaccount" style="display: none;">
                            <div class="form-group col-md-4">
                                <label class="form-label">Mobile Money Operator </label>
                                <select class="form-control" name="operator">
                                    <option>{{$payment->bank_name}}</option>
                                    <option value="">Select Operator</option>
                                    <option>AirtelMoney</option>
                                    <option>EzyPesa</option>
                                    <option>M-Pesa</option>
                                    <option>TigoPesa</option>
                                    <option>HaloPesa</option>
                                </select>
                            </div>
                        </div>
                        @endif

                        <div class="form-group col-md-12">
                            <label class="form-label">{{trans('navmenu.comments')}}</label>
                            <textarea class="form-control" name="comments" placeholder="Enter Comments (Optional)....">{{$payment->comments}}</textarea>
                        </div>
                        <div class="form-group col-md-4">
                                <button type="submit" class="btn btn-success">{{trans('navmenu.btn_save')}}</button>
                                <a href="javascript:history.back()" class="btn btn-warning">{{trans('navmenu.btn_cancel')}}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>                
    </div>
    <!--end row-->
@endsection