@extends('layouts.app')

@section('content')
    <!-- SELECT2 EXAMPLE -->
    <div class="card radius-6">
        <div class="card-header">
            <h4>{{$acctrans->reason}}</h4>
        </div>
        <!-- /.box-header -->
        <div class="card-body">
            <div class="row">
                <form class="form-validate" method="POST" action="{{route('acc-transactions.update', encrypt($acctrans->id))}}">
                    @csrf
                    {{ method_field('PATCH') }}
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.from')}} <span style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-control" name="from" required style="width: 100%;">
                                <option>{{$acctrans->from}}</option>
                                <option value="">Select Account</option>
                                <option>Cash</option>
                                <option>Bank</option>
                                <option>Mobile Money</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.to')}} <span style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-control" name="to" required style="width: 100%;">
                                <option>{{$acctrans->to}}</option>
                                <option value="">Select Account</option>
                                <option>Cash</option>
                                <option>Bank</option>
                                <option>Mobile Money</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.bank_name')}}</label>
                            <select class="form-control" name="bank_detail_id" style="width: 100%;">
                                @if(!is_null($acctrans->bank_detail_id))
                                <option value="{{$acctrans->bank_detail_id}}">{{App\BankDetail::find($acctrans->bank_detail_id)->bank_name}}</option>
                                @endif
                                <option value="">Select Account</option>
                                @foreach($bdetails as $bdetail)
                                <option value="{{$bdetail->id}}">{{$bdetail->bank_name}}</option>
                                @endforeach
                            </select>
                        </div>
    
                        <div class="col-md-6">
                            <label class="form-label">Amount <span style="color: red; font-weight: bold;">*</span></label>
                            <input type="number" name="amount" placeholder="Please enter Amount" class="form-control" value="{{$acctrans->amount}}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="register-username" class="label-control">Reason </label>
                            <input id="register-username" type="text" name="reason" required placeholder="Please enter Reason(Optional)" class="form-control" value="{{$acctrans->reason}}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.pick_date')}}</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" name="date" id="date" placeholder="{{trans('navmenu.pick_date')}}" class="form-control" value="{{$acctrans->date}}">
                            </div>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-md-6">
                        <button type="submit" class="btn btn btn-success">{{trans('navmenu.btn_save')}}</button>
                        <a href="javascript:history.back()" class="btn btn-warning">{{trans('navmenu.btn_cancel')}}</a>
                    </div>
                </form>
            </div>
            <!-- /.row -->
        </div>
    </div>
    <!-- /.box -->
@endsection

<link rel="stylesheet" href="../../css/DatePickerX.css">

<script src="../../js/DatePickerX.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="date"]'),
                $max = document.querySelector('[name="outdate"]');


            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : new Date()
            });

            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                // minDate    : new Date(),
                maxDate    : new Date()
            });

        });
    </script>