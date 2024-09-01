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
    <div class=" col-md-9 mx-auto"> 
        <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
        <hr>
        <div class="card">
            <div class="card-body">
                <form class="row g-3" method="POST" action="{{route('credit-notes.update', $creditnote->id)}}">
                    @csrf
                    {{ method_field('PATCH') }}
                    <div class="form-group col-md-4">
                        <label>CREDIT NOTE TO:</label>
                        <input type="text" name="customer" class="form-control" value="{{$creditnote->name}}" readonly>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Invoice No.</label>
                        <input type="text" name="inv_no" class="form-control" value="{{ sprintf('%05d', $creditnote->inv_no)}}" readonly>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Invoice Date :</label>
                        <input type="text" name="date" class="form-control" value="{{date('d, M Y', strtotime($creditnote->created_at))}}" readonly>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Reason for Credit Note<span style="color: red;">*</span></label>
                        <textarea class="form-control" name="reason" placeholder="Enter reason for issueing this Credit Note">{{$creditnote->reason}}</textarea>
                    </div>
                    <div class="form-group col-md-4">
                        <label>{{trans('navmenu.amount')}}</label>
                        <input type="number" name="amount" class="form-control" required value="{{$creditnote->amount}}" placeholder="Please enter Amount credited">
                    </div>
                    <div class="form-group col-md-12">
                        <button class="btn btn-primary pull-right" style="margin-left: 5px;">Update</button>
                        <a href="{{url('cancel-credit-note/'.Crypt::encrypt($creditnote->id))}}" class="btn btn-warning pull-right">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection