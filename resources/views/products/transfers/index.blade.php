@extends('layouts.app')
<script type="text/javascript">
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
            document.getElementById('delete-form-'+id).submit();
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
        	<div class="card radius-10">
        		<div class="card-body">
        			<div class="d-flex align-items-center">
        				
                        <div class="d-flex align-items-end px-1 py-1">
                            <ul class="nav nav-pills nav-pills-primary" role="tablist"  >
        						<li class="nav-item" role="presentation">
        							<a class="nav-link active" data-bs-toggle="tab" href="#tab_1" role="tab" aria-selected="false">
        								<div class="d-flex align-items-center">
        									<div class="tab-icon"><i class='bx bx-list-plus font-18 me-1'></i>
        									</div>
        									<div class="tab-title">Transfer Orders</div>
        								</div>
        							</a>
        						</li>
        						<!-- <li class="nav-item" role="presentation">
        							<a class="nav-link" data-bs-toggle="tab" href="#tab_2" role="tab" aria-selected="false">
        								<div class="d-flex align-items-center">
        									<div class="tab-icon"><i class='bx bx-plus font-18 me-1'></i>
        									</div>
        									<div class="tab-title">Export Transfer Orders</div>
        								</div>
        							</a>
        						</li> -->
        						<li class="nav-item" role="presentation">
        							<a class="nav-link"  href="{{route('transfer-orders.create')}}"  >
        								<div class="d-flex align-items-center">
        									<div class="tab-icon"><i class='bx bx-plus-circle font-18 me-1'></i>
        									</div>
        									<div class="tab-title">New Transfer Order</div>
        								</div>
        							</a>
        						</li>
        					</ul>
        				</div>
        			</div>
        			<div class="tab-content py-1">
        				<div class="tab-pane fade show active table-responsive" id="tab_1" role="tabpanel">
                            <hr>
                            <table id="example1" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>{{trans('navmenu.date')}}</th>
                                        <th>{{trans('navmenu.sto_no')}}</th>
                                        <th>{{trans('navmenu.source_shop')}}</th>
                                        <th>{{trans('navmenu.destin_shop')}}</th>
                                        <th>{{trans('navmenu.transfer_by')}}</th>
                                        <th style="text-align: center;">{{trans('navmenu.transfer_type')}}</th>
                                        <th>{{trans('navmenu.created_at')}}</th>
                                        <th>{{trans('navmenu.actions')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $i => $order)
                                    <tr>
                                        <td>{{$order->order_date}}</td>
                                        <td> <a href="{{route('transfer-orders.show', Crypt::encrypt($order->id))}}"> {{ sprintf('%05d', $order->order_no)}}</a></td>
                                        <td>{{$shop->name}}</td>
                                        <td>{{\App\Models\Shop::find($order->destination_id)->name}}</td>
                                        <td>{{\App\Models\User::find($order->user_id)->first_name}}</td>
                                        <td>{{$order->created_at}}</td>
                                        <?php
                                            if($order->is_transfomation_transfer) {
                                                $transfer_type_en = "Transformation";
                                                $transfer_type_sw = "Kubadilisha";
                                            }else{
                                                $transfer_type_en = "Normal";
                                                $transfer_type_sw = "Kawaida";
                                            }
                                        ?>
                                         @if($order->is_transfomation_transfer)
                                        <td style="text-align: center;">
                                            <span class="badge bg-warning">
                                            @if(app()->getLocale() == 'en'){{$transfer_type_en}}@else{{$transfer_type_sw}}@endif
                                            </span>
                                        </td>
                                        @else
                                        <td style="text-align: center;">
                                            <span class="badge bg-success">
                                            @if(app()->getLocale() == 'en'){{$transfer_type_en}}@else{{$transfer_type_sw}}@endif
                                            </span>
                                        </td>
                                        @endif
                                        <td>
                                            <a href="{{route('transfer-orders.edit', encrypt($order->id))}}">
                                                <i class="bx bx-edit" style="color: blue;"></i>
                                            </a>
                                            <form id="delete-form-{{$i}}" method="POST" action="{{route('transfer-orders.destroy' , encrypt($order->id))}}" style="display: inline;">
                                                @csrf         
                                                @method('DELETE')
                                                <a href="#" class="button" onclick="confirmDelete('{{$i}}')"><i class="bx bx-trash" style="color: red;"></i></a>
                                            </form> 
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>                        
                            </table>
                        </div>

        				<div class="tab-pane fade" id="tab_2" role="tabpanel">
                            <table id="example5" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Transfer Order #</th>
                                        <th>Source</th>
                                        <th>Destination</th>
                                        <th>Created By</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $i => $order)
                                    <tr>
                                        <td>{{$order->order_date}}</td>
                                        <td> {{ sprintf('%05d', $order->order_no)}}</td>
                                        <td>{{$shop->display_name}}</td>
                                        <td>{{\App\Models\Shop::find($order->destination_id)->name}}</td>
                                        <td>{{\App\Models\User::find($order->user_id)->first_name}}</td>
                                        <td>{{$order->created_at}}</td>
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
@endsection

  