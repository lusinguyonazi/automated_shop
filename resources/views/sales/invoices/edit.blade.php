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
                <div class="card-body">
                    <form class="row g-3" method="POST" action="{{route('invoices.update', encrypt($invoice->id))}}">
                        @csrf
                        {{ method_field('PATCH') }}
                        <input type="hidden" name="id" value="{{$invoice->id}}">
                        @if($settings->inv_no_type == 'Manuall')
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="invoice" class="form-label">Invoice Number</label>
                                <input type="text" class="form-control" id="invoice" placeholder="Enter Invoice Number" name="inv_no" value="{{$invoice->inv_no}}" />
                            </div> 
                        </div>
                        @endif
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Customer <span style="color: red; font-weight: bold;">*</span></label>
                                <select class="form-select select2" name="an_sale_id" required style="width: 100%;">
                                    <option value="{{$currsale->id}}">{{$currsale->name}}, {{$currsale->created_at}}</option>
                                    <option></option>
                                    @foreach($sales as $key => $sale)
                                    <option value="{{$sale->id}}">{{$sale->name}}, {{$sale->created_at}}</option>
                                    @endforeach                      
                                </select>
                            </div>
                        </div>
                        @if($settings->is_filling_station)
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="total" class="form-label">{{trans('navmenu.vehicle_no')}}</label>
                                <input type="text" class="form-control" id="vehicle_no" placeholder="{{trans('navmenu.vehicle_no')}}" name="vehicle_no" value="{{$invoice->vehicle_no}}" />
                            </div> 
                        </div>
                        @endif
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Due date <span style="color: red; font-weight: bold;">*</span></label>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" name="due_date" placeholder="Choose Due date" class="form-control" value="{{$invoice->due_date}}">
                                </div>
                                <!-- /.input group -->
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Bank Details <span style="color: red; font-weight: bold;">*</span></label>
                                <select class="form-control" name="bank_detail_id" style="width: 100%;">
                                    @if(!is_null($bankdetail))
                                    <option value="{{$invoice->bank_detail_id}}">{{$bankdetail->bank_name}}({{$bankdetail->account_number}})</option>
                                    @endif
                                    <option value="">Select Bank Account</option>
                                    @foreach($bdetails as $key => $detail)
                                    <option value="{{$detail->id}}">{{$detail->bank_name}}({{$detail->account_number}})</option>
                                    @endforeach                      
                                </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="form-label">Note</label>
                                <textarea name="note" class="form-control">{{$invoice->note}}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn btn-success">Save</button>
                                <a href="javascript:history.back()" class="btn btn-warning">Cancel</a>
                            </div>
                        </div>
                        <!-- /.col -->
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection