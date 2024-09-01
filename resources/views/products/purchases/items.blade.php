@extends('layouts.app')
<script type="text/javascript">
    function weg(elem) {
      var x = document.getElementById("date_field");
      if(elem.value !== "auto") {
        x.style.display = "block";
      } else {
        x.style.display = "none";
        $("#datepicker").val('');
      }
    }

    function confirmDelete(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure_delete')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            window.location.href="{{url('delete-stock/')}}/"+id;
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
    }

    function confirmDeletePayment(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure_delete')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            window.location.href="{{url('purchase-payments/destroy/')}}/"+id;
            Swal.fire(
              "{{trans('navmenu.deleted')}}",
              "{{trans('navmenu.cancelled')}}",
              'success'
            )
          }
        })
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
            <div  class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
                <div class="col">
                    <div class="card radius-10 ">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">{{trans('navmenu.supplier')}}</p>
                                    <h4 class="my-1">@if(!is_null($supplier)){{$supplier->name}}@else {{trans('navmenu.unknown')}} @endif</h4>
                                </div>
                                <div class="widgets-icons bg-light-primary text-primary ms-auto"><i class="bx bxs-box"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.col -->
                <div class="col">
                    <div class="card radius-10 ">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">{{trans('navmenu.total_amount')}}</p>
                                    <h4 class="my-1">{{number_format($purchase->total_amount, 2, '.', ',')}}</h4>
                                </div>
                                <div class="widgets-icons bg-light-warning text-warning ms-auto"><i class="bx bx-money"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.col -->
                <div class="col">
                    <div class="card radius-10 ">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">{{trans('navmenu.amount_paid')}}</p>
                                    <h4 class="my-1">{{number_format($purchase->amount_paid, 2, '.', ',')}}</h4>
                                </div>
                                <div class="widgets-icons bg-light-info text-info ms-auto"><i class="bx bx-money"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.col -->
                <div class="col">
                    <div class="card radius-10 ">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">{{trans('navmenu.unpaid')}}</p>
                                    <h4 class="my-1">{{number_format($purchase->total_amount-$purchase->amount_paid, 2, '.', ',')}}</h4>
                                </div>
                                <div class="widgets-icons bg-light-danger text-danger ms-auto"><i class="bx bx-money"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.col -->
            </div>

              <!-- =========================================================== -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card radius-10">
                        <div class="card-header">
                            <button type="button" class="btn btn-success pull-right" data-bs-toggle="modal" data-bs-target="#itemModal">
                                <i class="bx bx-shopping-bag"></i>
                                    {{trans('navmenu.add_purchase_item')}}
                            </button>
                        </div>
                        <div class="card-body">
                            <table id="example1" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <th>#</th>
                                    <th>{{trans('navmenu.product_name')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                    <th style="text-align: center;">UOM</th>
                                    <th style="text-align: center;">{{trans('navmenu.unit_cost')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.total')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.purchase_date')}}</th>
                                    <th>{{trans('navmenu.actions')}}</th>
                                </thead>
                                <tbody>
                                    @foreach($pitems as $index => $stock)
                                        <tr>
                                            <td>{{$index+1}}</td>
                                            <td>{{$stock->name}}</td>
                                            <td style="text-align: center;">{{number_format($stock->quantity_in)}}</td>
                                            <td> <span style="color: gray;">{{$stock->basic_unit}}</span></td>
                                            <td style="text-align: center;">{{number_format($stock->buying_per_unit, 2, '.', ',')}}</td>
                                            <td style="text-align: center;">{{number_format($stock->buying_per_unit*$stock->quantity_in, 2, '.', ',')}}</td>
                                            <td style="text-align: center;">{{$stock->time_created}}</td>
                                            <td style="text-align: center;">
                                                <a href="{{route('stocks.edit' , Crypt::encrypt($stock->id))}}"><i class="bx bx-edit" style="color: blue;"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">{{trans('navmenu.purchase_payments')}}</h5>
                        </div>
                        <div class="card-body">
                            @csrf
                            <table id="del-multiple" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>{{trans('navmenu.pay_date')}}</th>
                                        <th>{{trans('navmenu.amount')}}</th>
                                        <th>{{trans('navmenu.account')}}</th>
                                        <th>{{trans('navmenu.record_at')}}</th>
                                        <th>{{trans('navmenu.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $index => $payment)
                                        <tr>
                                            <td style="text-align: center;">{{$payment->id}}</td>
                                            <td style="text-align: center;">{{$payment->pay_date}}</td>
                                            <td style="text-align: center;">{{number_format($payment->amount, 2, '.', ',')}}</td>
                                            <td style="text-align: center;">
                                                @if($payment->account == 'Cash')
                                                    @if(app()->getLocale() == 'en')
                                                        {{$payment->account}}
                                                    @else
                                                        {{trans('navmenu.cash')}}
                                                    @endif
                                                @elseif($payment->account == 'Mobile Money')
                                                    @if(app()->getLocale() == 'en')
                                                        {{$payment->account}}
                                                    @else
                                                        {{trans('navmenu.mobilemoney')}}
                                                    @endif
                                                @elseif($payment->account == 'Bank')
                                                    @if(app()->getLocale() == 'en')
                                                        {{$payment->account}}
                                                    @else
                                                        {{trans('navmenu.bank')}}
                                                    @endif                           
                                                @endif
                                            </td>
                                            <td style="text-align: center;">{{$payment->created_at}}</td>
                                            <td style="text-align: center;">
                                                <a href="{{ route('purchase-payments.edit', encrypt($payment->id))}}">
                                                    <i class="bx bx-edit" style="color: blue;"></i>
                                                </a>
                                                <a href="#" onclick="confirmDeletePayment('<?php echo Crypt::encrypt($payment->id); ?>')">
                                                    <i class="bx bx-trash" style="color: red;"></i>
                                                </a>      
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
    </div>

<div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="pull-left" id="myModalLabel">{{trans('navmenu.add_purchase_item')}}</h5>
                <button type="button"  class="close btn btn-danger pull-right" data-bs-dismiss="modal" aria-label="Close"><span class="bx bx-x-circle"></span></button>
                
            </div>
            <form class="form-validate" method="POST" action="{{url('add-purchase-item')}}">
                <div class="modal-body row">
                    @csrf
                    <input type="hidden" name="purchase_id" value="{{$purchase->id}}">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">{{trans('navmenu.product_name')}} <span style="color: red; font-weight: bold;">*</span></label>
                        <select class="form-control form-control-sm mb-3 select2" id="my-select" name="product_id" required style="width: 100%;">
                            <option value="">Select Product</option>
                            @foreach($products as $key => $product)
                            <option value="{{$product->id}}">{{$product->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{trans('navmenu.quantity')}} <span style="color: red; font-weight: bold;">*</span></label>
                        <input id="name" type="number" name="quantity_in" placeholder="{{trans('navmenu.hnt_enter_quantity')}}" class="form-control form-control-sm mb-3" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{trans('navmenu.buying_per_unit')}}</label>
                        <input id="unit_price" type="number" min="0" name="buying_per_unit" placeholder="{{trans('navmenu.hnt_buying_price')}}" class="form-control form-control-sm mb-3" required>
                    </div>
                </div>                    
                <div class="modal-footer">
                    <button type="submit" class="btn btn btn-success">Save</button>
                    <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
   
@endsection 


<link rel="stylesheet" href="../css/DatePickerX.css">

<script src="../js/DatePickerX.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="mnf_date"]'),
                $max = document.querySelector('[name="exp_date"]');

            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    :  new Date(),
            });

            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                minDate    : $min,
                // maxDate    : new Date()
            });

        });
    </script>