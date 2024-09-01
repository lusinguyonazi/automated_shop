@extends('layouts.vfd')
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
    <div class="row row-cols-1 row-cols-md-1 row-cols-lg-1 row-cols-xl-1">
        <div class="col">
            <h6 class="mb-0 text-uppercase">{{$title}}</h6>
            <hr/>
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-pills nav-pills-warning mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="pill" href="#warning-pills-home" role="tab" aria-selected="true">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-receipt font-18 me-1'></i></div>
                                    <div class="tab-title">{{trans('navmenu.receipt_sent')}}</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="pill" href="#warning-pills-ack" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-download font-18 me-1'></i></div>
                                    <div class="tab-title">{{trans('navmenu.receipt_ack')}}</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="warning-pills-home" role="tabpanel">
                            <form id="frm-example" action="{{url('delete-multiple-receipts')}}" method="POST">
                                @csrf
                                <div class="table-responsive">
                                    <table id="del-multiple" class="table table-striped table-bordered display nowrap" style="width:100%; font-size: 14px;">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>{{trans('navmenu.date')}}</th>
                                                <th>{{trans('navmenu.regid')}}</th>
                                                <th>{{trans('navmenu.cust_id_type')}}</th>
                                                <th>{{trans('navmenu.efdserial')}}</th>
                                                <th>{{trans('navmenu.rctnum')}}</th>
                                                <th>{{trans('navmenu.created_at')}}</th>
                                                <th>{{trans('navmenu.actions')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($rctinfos as $i => $rct)
                                            <tr>
                                                <td>{{$i+1}}</td>
                                                <td>{{$rct->date}}</td>
                                                <td>{{$rct->regid}}</td>
                                                <td>
                                                    @foreach($custids as $cidt)
                                                    @if($cidt['id'] == $rct->custidtype)
                                                    {{$cidt['name']}}
                                                    @endif
                                                    @endforeach
                                                </td>
                                                <td>{{$rct->efdserial}}</td>
                                                <td>{{$rct->rctnum}}</td>
                                                <td>{{$rct->created_at}}</td>
                                                <td>
                                                    @if(is_null($rct->ackcode) || $rct->ackcode != 0)
                                                    <a href="{{ url('submit-receipt/'.encrypt($rct->id))}}"><i class="bx bx-send"></i>Submit VFD</a> |@endif 
                                                    <a href="{{ route('vfd-rct-infos.show', encrypt($rct->id))}}"><i class="bx bx-receipt"></i></a> | 
                                                    <a href="{{route('vfd-rct-infos.edit', encrypt($rct->id))}}"><i class="bx bx-edit"></i></a> | 
                                                    @if($rct->status != 'Submitted')
                                                    <form id="delete-form-{{$i}}" method="POST" action="{{ route('vfd-rct-infos.destroy', encrypt($rct->id))}}" style="display: inline;"> 
                                                        @csrf
                                                        @method("DELETE")
                                                        <a href="#" onclick="confirmDelete('<?php echo $i; ?>')"><i class="bx bx-trash" style="color: red;"></i></a>
                                                    </form>
                                                    @endif 
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <button id="submitButton" class="btn btn-danger">{{trans('navmenu.delete_selected')}}</button>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="warning-pills-ack" role="tabpanel">
                            <div class="table-responsive">
                                <table id="example2" class="table table-striped table-bordered display nowrap" style="width:100%; font-size: 14px;">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>{{trans('navmenu.date')}}</th>
                                            <th>{{trans('navmenu.regid')}}</th>
                                            <th>{{trans('navmenu.cust_id_type')}}</th>
                                            <th>{{trans('navmenu.efdserial')}}</th>
                                            <th>{{trans('navmenu.rctnum')}}</th>
                                            <th>{{trans('navmenu.ackcode')}}</th>
                                            <th>{{trans('navmenu.ackmsg')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rctinfos as $i => $rct)
                                        <tr>
                                            <td>{{$i+1}}</td>
                                            <td>{{$rct->date}}</td>
                                            <td>{{$rct->regid}}</td>
                                            <td>
                                                @foreach($custids as $cidt)
                                                @if($cidt['id'] == $rct->custidtype)
                                                {{$cidt['name']}}
                                                @endif
                                                @endforeach
                                            </td>
                                            <td>{{$rct->efdserial}}</td>
                                            <td>{{$rct->rctnum}}</td>
                                            <td>{{$rct->ackcode}}</td>
                                            <td>{{$rct->ackmsg}}</td>
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
    </div>
    <!--end row-->
@endsection