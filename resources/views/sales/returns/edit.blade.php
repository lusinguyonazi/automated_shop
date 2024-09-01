@extends('layouts.app')
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
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
    <div class=" col-md-10 mx-auto"> 
        <h3>{{$title}}</h3>
        <hr>
        <div class="card">
            <div class="card-body">
                <form class="row g-3" method="POST" action="{{route('sales-returns.update', $salereturn->id)}}">
                    @csrf
                    {{ method_field('PATCH') }}
                    <div class="form-group col-md-4">
                        <label class="form-label">SALE RETURN FROM::</label>
                        <input type="text" name="customer" class="form-control form-control-sm mb-3" value="{{$salereturn->name}}" readonly>
                    </div>
                    <div class="form-group col-md-4">
                        <label class="form-label">Sale Date :</label>
                        <input type="text" name="date" class="form-control form-control-sm mb-3" value="{{date('d, M Y', strtotime($salereturn->created_at))}}" readonly>
                    </div>
                    <div class="form-group col-md-4" style="text-align: right;">
                        <button class="btn btn-primary">Create</button>
                        <a href="javascript:history.back()" class="btn btn-warning">Cancel</a>
                    </div>
                    <div class="form-group col-md-8">
                        <label class="form-label">Reason for Credit Note<span style="color: red;">*</span></label>
                        <textarea class="form-control form-control-sm mb-3" name="reason" placeholder="Enter reason for issueing this Credit Note">{{$salereturn->reason}}</textarea>
                    </div>
                </form>
                <hr>
                <form class="row g-3" action="{{route('sale-return-items.store')}}" method="POST">
                    @csrf
                    <input type="hidden" name="sale_return_id" value="{{$salereturn->id}}">
                    <div class="form-group col-md-6">
                        <label class="form-label">Select Item Returned</label>
                        <select name="product_id" class="form-control form-control-sm mb-3" onchange='if(this.value != 0) { this.form.submit(); }' required>
                            <option value="">Select Item</option>
                            @foreach($items as $item)
                            <option value="{{$item->product_id}}">{{$item->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </form>

                <div id="inv-content">

                    <div class="invoice-content">
                        <table border="0" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th class="desc">Description</th>
                                    <th class="qty">Quantity</th>
                                    <th class="unit">Unit price</th>
                                    <th class="total">Total</th>
                                    <th class="del"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sritems as $key => $item)
                                <tr>
                                    <td> {{$key+1}} </td>
                                    <td class="desc">{{$item->name}}</td>
                                    <form id="item-form{{$key}}" class="form" method="POST" action="{{route('sale-return-items.update', $item->id)}}">
                                        @csrf
                                        {{ method_field('PATCH') }} 
                                        <td class="qty">
                                        <input type="hidden" name="sale_return_id" value="{{$salereturn->id}}">
                                        <input id="input_qty{{$key}}" type="number" name="quantity" value="{{number_format($item->quantity)}}" style="text-align: center;">
                                        <script>
                                            $(document).ready(function(){
                                                $("#input_qty"+"<?php echo $key ?>").blur(function() { 
                                                    $("#item-form"+"<?php echo $key ?>").submit(); 
                                                });
                                            });
                                        </script>
                                        </td>
                                    </form>
                                    <td class="unit">{{number_format($item->price_per_unit)}}</td>
                                    <td class="total">{{number_format($item->price)}}</td>
                                    <td class="del">
                                        <a href="{{url('sale-return-items/destroy/'.encrypt($item->id))}}"> <i class="fa fa-trash" style="color: red;"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="no-break">
                            <table class="grand-total">
                                <tbody>
                                    <tr>
                                        <td class="desc"></td>
                                        <td class="unit" colspan="2">TOTAL:</td>
                                        <td class="total">{{number_format($total)}}</td>
                                        <td class="del"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection