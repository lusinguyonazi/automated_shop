@extends('layouts.vfd')

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
                    <div class="table-responsive">
                        <a href="{{ route('vfd-zreports.create') }}" class="btn btn-primary">Create ZReport</a>
                        <table id="example2" class="table table-striped table-bordered display nowrap" style="width:100%; font-size: 14px;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{trans('navmenu.date')}}</th>
                                    <th>{{trans('navmenu.znumber')}}</th>
                                    <th>{{trans('navmenu.regid')}}</th>
                                    <th>{{trans('navmenu.tin')}}</th>
                                    <th>{{trans('navmenu.efdserial')}}</th>
                                    <th>{{trans('navmenu.ackcode')}}</th>
                                    <th>{{trans('navmenu.ackmsg')}}</th>
                                    <th>{{trans('navmenu.actions')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($zreports as $i => $zrep)
                                <tr>
                                    <td>{{$i+1}}</td>
                                    <td>{{$zrep->date}}</td>
                                    <td><a href="{{ route('vfd-zreports.show', encrypt($zrep->id))}}">{{$zrep->znumber}}</a></td>
                                    <td>{{$zrep->regid}}</td>
                                    <td>{{$zrep->tin}}</td>
                                    <td>{{$zrep->efdserial}}</td>
                                    <td>{{$zrep->ackcode}}</td>
                                    <td>{{$zrep->ackmsg}}</td>
                                    <td>

                                        @if($zrep->ackcode != 0)
                                        <a href="{{ url('submit-zreport/'.encrypt($zrep->id))}}"><i class="bx bx-send"></i>Re-Submit ZReport</a> | @endif
                                        <a href="{{ route('vfd-zreports.show', encrypt($zrep->id))}}"><i class="bx bx-detail"></i></a>
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
    <!--end row-->
@endsection