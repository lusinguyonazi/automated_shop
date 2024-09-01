@extends('layouts.app')

<script type="text/javascript">
    function saveChanges(key) {
        document.getElementById('item-form'+key).submit();
    }
</script>
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
            <div class="card">
                <div class="card-body">
                    <form class="row g-3 form" method="POST" action="{{route('transfer-orders.update', Crypt::encrypt($transorder->id))}}">
                        @csrf
                        {{ method_field('PATCH') }} 
                        
                        <div class="col-md-2">
                            <label class="form-label">Order No. <span style="color: red; font-weight: bold;">*</span></label>
                            <input type="text" name="order_no" class="form-control" placeholder="Enter your order Number" value="TO-{{sprintf('%05d', $transorder->order_no)}}" required readonly>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Date <span style="color: red; font-weight: bold;">*</span></label>
                            <div class="inner-addon left-addon">
                                <i class="fa fa-calendar"></i>
                                <input type="text" name="order_date" id="orderdatepicker" required class="form-control" placeholder="Select date of transfer" value="{{$transorder->order_date}}" aria-describedby="calendar">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Source Shop/Store <span style="color: red; font-weight: bold;">*</span></label>
                            <input type="text" name="shop_id" value="{{$shop->name}}" readonly="" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Destination Shop/Store <span style="color: red; font-weight: bold;">*</span></label>
                            <select class="form-select form-select-sm mb-3" name="destin_id" ng-model="destin_id" ng-change="getDestin(destin_id)"  required>
                                <option value="{{$destinshop->id}}">{{$destinshop->name}}</option>
                                <option value="">Select Shop/Store</option>
                                @foreach($destinations as $key => $destin)
                                <option value="{{$destin->id}}">{{$destin->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Reason <span style="color: red; font-weight: bold;">*</span></label>
                            <textarea name="reason" class="form-control" required placeholder="Please type here the reason of transfer">{{$transorder->reason}}</textarea>
                        </div>
                        <div class="col-md-12">
                            <button class="btn btn-success float-end" type="submit"><i class="bx bx-file"></i> Save Order</button>
                            <a href="{{url('transfer-orders')}}" class="btn btn-warning float-end" id="btn-create" style="margin-right: 5px;"><i class="lni lni-close"></i> Cancel</a>
                        </div>
                    </form>
                    <div class="col-md-12 order-items ms-2 me-2 ">
                        <h6 class="mb-0 text-uppercase text-center">Transfer Items</h6>
                        <hr>
                        <table border="0" cellspacing="0" cellpadding="0" class="table item-table"  >
                            <thead>
                                <tr>
                                    <th class="Item">Item name</th>
                                    <th class="source">Source Stock</th>
                                    <th class="destin">Destin Stock</th>
                                    <th class="qty">Quantity</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orderitems as $key => $item)
                                <tr>    
                                    <td class="item">{{$item->product->name}}</td>
                                    <td class="source">{{number_format($item->source_stock)}}</td>
                                    <td class="destin">{{number_format($item->destin_stock)}}</td>
                                    <form id="item-form{{$key}}" class="form" method="POST" action="{{url('update-transorder-item')}}">
                                        <td class="qty">
                                            @csrf
                                            <input type="hidden" name="id" value="{{$item->id}}">
                                            <input type="hidden" name="transfer_order_id" value="{{$transorder->id}}">
                                            <input id="input_qty{{$key}}" type="number" name="quantity" value="{{number_format($item->quantity)}}" style="text-align: center;" onblur="saveChanges('<?php echo $key; ?>')">
                                        </td>
                                    </form>
                                    <td class="del">
                                        <a href="#" onclick="confirmDelete('<?php echo Crypt::encrypt($item->id) ?>')"><i class="bx bx-trash"  style="color: red;"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection