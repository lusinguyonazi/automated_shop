@extends('layouts.vfd')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3"></div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{url('home')}}"><i class="bx bx-home-alt"></i></a> </li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-xl-11 mx-auto">
            <h6 class="mb-0 text-uppercase">VFD Registration Info</h6>
            <hr/>
            <div class="card">
                <div class="card-body">
                    @if(!is_null($reginfo) && !is_null($reginfo->ackcode))
                    <div class="p-4 border rounded">
                        <h6>Registration Response</h6>
                        <hr>
                        <table class="table mb-0 table-striped">
                            <tbody>
                                <tr>
                                    <td>ACKCODE: </td><td>{{$reginfo->ackcode}}</td>
                                    <td>ACKMSG: </td><td>{{$reginfo->ackmsg}}</td>
                                </tr>
                                <tr>
                                    <td>REGID: </td><td>{{$reginfo->regid}}</td>
                                    <td>SERIAL: </td><td>{{$reginfo->serial}}</td>
                                </tr>
                                <tr>
                                    <td>UIN: </td><td>{{$reginfo->uin}}</td>
                                    <td>TIN: </td><td>{{$reginfo->tin}}</td>
                                </tr>
                                <tr>
                                    <td>VRN: </td><td>{{$reginfo->vrn}}</td>
                                    <td>MOBILE: </td><td>{{$reginfo->mobile}}</td>
                                </tr>
                                <tr>
                                    <td>STREET: </td><td>{{$reginfo->street}}</td>
                                    <td>CITY: </td><td>{{$reginfo->city}}</td>
                                </tr>
                                <tr>
                                    <td>ADDRESS: </td><td>{{$reginfo->address}}</td>
                                    <td>COUNTRY: </td><td>{{$reginfo->country}}</td>
                                </tr>
                                <tr>
                                    <td>NAME: </td><td>{{$reginfo->name}}</td>
                                    <td>RECEIPTCODE: </td><td>{{$reginfo->receiptcode}}</td>
                                </tr>
                                <tr>
                                    <td>REGION: </td><td>{{$reginfo->region}}</td>
                                    <td>ROUTINGKEY: </td><td>{{$reginfo->routing_key}}</td>
                                </tr>
                                <tr>
                                    <td>GC: </td><td>{{$reginfo->gc}}</td>
                                    <td>TAXOFFICE: </td><td>{{$reginfo->taxoffice}}</td>
                                </tr>
                                <tr>
                                    <td>USERNAME: </td><td>{{$reginfo->username}}</td>
                                    <td>PASSWORD: </td><td>{{$reginfo->password}}</td>
                                </tr>
                                <tr>
                                    <td>TOKENPATH: </td><td>{{$reginfo->tokenpath}}</td>
                                    <td>TAXCODE: </td><td>{{$reginfo->taxcode}}</td>
                                </tr>
                                <tr>
                                    <td>TOKEN TYPE: </td><td>{{$reginfo->token_type}}</td>
                                    <td>TOKEN EXPIRES ON: </td><td>{{\Carbon\Carbon::parse($reginfo->reg_date)->addSeconds($reginfo->expires_in)}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info border-0 bg-info alert-dismissible fade show py-2">
                        <div class="d-flex align-items-center">
                            <div class="font-35 text-dark"><i class='bx bx-info-square'></i></div>
                            <div class="ms-3">
                                <h6 class="mb-0 text-dark">Info</h6>
                                <div class="text-dark">Sorry! Your not registere to use this service module. Please contact us for Registration.</div>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection