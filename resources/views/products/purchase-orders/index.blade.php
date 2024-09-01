
@extends('layouts.app')

<script>
    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this record?')) {
            document.getElementById('delete-form-'+id).submit();
        }
    }

    function showModal(id) {
        $('#id_hide').val(id);
        $('#payModal').modal('show');
    }

</script>

@section('content')

<div class="row">
    <div class="col-xl-12">
        <div class="card">
                
            <div class="card-body">
            <!-- Custom Tabs (Pulled to the right) -->
                <h4>{{trans('navmenu.porders')}}</h4>
                <div class="d-lg-flex">
                        <a   class="btn btn-primary" href="{{route('purchase-orders.create')}}"><i class="bx bx-cart-plus"></i>  {{trans('navmenu.new_purchase_order')}}</a>
                </div>
                <div>
                   
                    <table id="del-multiple" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                        <thead>
                            <tr>
                                <th></th>
                                <th>{{trans('navmenu.date')}}</th>
                                <th>{{trans('navmenu.supplier')}}</th>
                                <th>{{trans('navmenu.order_no')}}</th>
                                <th>{{trans('navmenu.amount')}}</th>
                                <th>{{trans('navmenu.status')}}</th>
                                <th>{{trans('navmenu.created_at')}}</th>    
                                <th>{{trans('navmenu.actions')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($porders as $index => $order)
                            <tr>
                                <td>{{$order->id}}</td>
                                <td>{{date('d-m-Y', strtotime($order->time_created))}}</td>
                                @if(!is_null($order->supplier_id) && !is_null(App\Models\Supplier::find($order->supplier_id)))
                                <td><a href="{{ url('poitems/'.encrypt($order->id))}}">{{App\Models\Supplier::find($order->supplier_id)->name}}</a></td>
                                @else
                                <td><a href="{{ url('poitems/'.encrypt($order->id))}}">{{trans('navmenu.unknown')}}</a></td>
                                @endif
                                <td><a href="{{ route('purchase-orders.show', encrypt($order->id))}}">{{ sprintf('%04d', $order->order_no)}}</a></td>
                                <td>{{number_format($order->amount)}}</td>
                                <td>{{$order->status}}</td>
                                <td>{{$order->created_at}}</td>
                                <td>
                                    @if($order->status == 'Pending')
                                        <a href="{{ route('purchase-orders.edit', encrypt($order->id))}}"><i class="fa fa-edit" style="color: blue;"></i></a> | 
                                        <form id="delete-form-{{$order->id}}" method="POST" action="{{ route('purchase-orders.destroy', encrypt($order->id))}}" style="display: inline;"> 
                                            @csrf
                                            @method("DELETE")
                                            <a href="javascript:;" class="text-danger" onclick=" return confirmDelete('<?php echo $order->id; ?>')"><i class='bx bx-trash'></i></a>
                                        </form>
                                    @endif     
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <form id="frm-example" action="{{url('delete-multiple-porders')}}" method="POST">
                        @csrf
                        <button id="submitButton" class="btn btn-danger">{{trans('navmenu.delete_selected')}}</button>
                    </form>
                 </div>
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
</div>
@endsection

<link rel="stylesheet" href="css/DatePickerX.css">

<script src="js/DatePickerX.min.js"></script>
<script>
    window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="pay_date"]'),
                $max = document.querySelector('[name="sale_date"]');

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