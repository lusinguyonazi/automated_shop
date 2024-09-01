@extends('layouts.prod')
<script>
    function confirmDelete(id) {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure')}}",
          text: "{{trans('navmenu.no_revert')}}",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.cancel_it')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            document.getElementById('delete-form-' + id).submit();
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
    <div class="col-md-11 mx-auto">
        <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
        <hr>
        <div class="row">
            <form class="dashform form-horizontal " action="{{url('prod-cost')}}" method="POST" id="stockform">
                @csrf
                <input type="hidden" name="start_date" id="start_input" value="">
                <input type="hidden" name="end_date" id="end_input" value="">
                
                <div class="float-sm-end">
                  <div class="input-group">
                      <button type="button" class="btn btn-white btn-sm" id="reportrange">
                        <span><i class="bx bx-calendar"></i></span>
                        <i class="bx bx-caret-down"></i>
                      </button>
                    </div>
                </div>
                
            </form>
        </div>
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs nav-success" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" data-bs-toggle="tab" href="#tab_1-1" role="tab" aria-selected="true">
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='bx bx-list-plus font-18 me-1'></i>
                                </div>
                                <div class="tab-title">{{trans('navmenu.production_records')}}</div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link"  href="{{route('prod-costs.create')}}" >
                            <div class="d-flex align-items-center">
                                <div class="tab-icon"><i class='bx bx-list-check font-18 me-1'></i>
                                </div>
                                <div class="tab-title">{{trans('navmenu.production_costs')}}</div>
                            </div>
                        </a>
                    </li>
                </ul>
                <div class="tab-content py-3">
                    <div class="tab-pane fade show active" id="tab_1-1" role="tabpanel">
                        <form id="frm-example" action="{{url('delete-multiple-materials')}}" method="POST">
                            @csrf
                            <button id="submitButton" class="btn btn-danger btn-sm">{{trans('navmenu.delete_selected')}}</button>
                        </form>
                            
                            <div class="table-responsive">
                                <table id="del-multiple" class="table table-striped table-bordered display nowrap" style="width: 100%; font-size: 14px;">
                                    <thead style="font-weight: bold; font-size: 14;">
                                        <tr>
                                           <th></th>
                                            <th>S/NO</th>
                                            <th>{{trans('navmenu.date')}}</th>
                                            <th>{{trans('navmenu.batch_no')}}</th>
                                            <th>{{trans('navmenu.production_volume')}}</th>
                                            <th>{{trans('navmenu.total_cost')}}</th>
                                            <th>{{trans('navmenu.status')}}</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       @foreach($prod_records as $index => $prod_record)
                                        <tr>
                                            <td></td>
                                            <td style="text-align: center;">{{$index +1}}</td>
                                            <td>{{date('d-m-Y', strtotime($prod_record->date))}}</td>
                                            <td style="text-align: center;">{{$prod_record->prod_batch}}</td>
                                            <td style="text-align: center;">{{$prod_record->total_prod_qty}}</td>
                                            <td style="text-align: center;">{{number_format($prod_record->total_cost)}}</td>
                                            <td>@if($prod_record->is_transferred){{trans('navmenu.transfered')}} @else {{trans('navmenu.waiting_transfer')}} @endif</td>

                                            <td>
                                                <a href="{{route('prod-costs.show', encrypt($prod_record->id))}}">
                                                    <span class="lni lni-eye" title="{{trans('navmenu.show')}}"></span>
                                                </a> |  
                                                @if(!$prod_record->is_transferred) 
                                                <a href="{{url('prod-transfer-to/'.encrypt($prod_record->id))}}">
                                                    <span class="bx bx-send" title="{{trans('navmenu.stock_transfer')}}"></span>
                                                </a> |
                                                @endif

                                                <form id="delete-form-{{$index}}" method="POST" action="{{route('prod-costs.destroy' , encrypt($prod_record->id))}}" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="#" onclick="confirmDelete('{{$index}}')">
                                                    <i class="bx bx-trash" style="color: red;"></i>
                                                </a> 
                                                </form>     
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                    </div>
                    <div class="tab-pane fade" id="tab_2-2" role="tabpanel">
                        <div class="row">
                            
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


