@extends('layouts.adm')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('admin/home')}}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">

        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="p-2 rounded">
                        <form class="form row g-3" method="post" action="{{ route('service-charges.store') }}" validate>
                            <h6 class="mb-2 mt-2 text-uppercase text-center">New Service Charge</h6>
                            {{csrf_field()}}
                            <div class="col-md-12">
                                <label class="form-label">Initial Payment</label>
                                <input class="form-control form-control-sm mb-1 border-primary" type="number" name="initial_pay" placeholder="Enter Initial Payment amount" id="userinput8" required>
                            </div>
                            <div class="col-md-12">
                                <label for="form-label">Next Payment</label>
                                <input class="form-control form-control-sm mb-1 border-primary" type="number" name="next_pay" placeholder="Enter Next Payment amount" id="userinput8" required>
                            </div>
                            <div class="col-md-12">
                                <label for="form-label">Duration</label>
                                <select class="form-control form-control-sm mb-1 border-primary+" id="userinput6" name="duration" required>
                                    <option></option>
                                    <option>Monthly</option>
                                    <option>Quarterly</option>
                                    <option>Semi Annually</option>
                                    <option>Annually</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Package</label>
                                <select class="form-control form-control-sm mb-1 border-primary+" id="userinput6" name="type" required>
                                    <option></option>
                                    <option value="1">Standard</option>
                                    <option value="2">Premium</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ url('admin/service-charges') }}" class="btn btn-warning btn-sm">Cancel</a>
                                <button type="submit" class="btn btn-primary btn-sm">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3 mb-2 text-uppercase text-center">{{$title}}</h6>
                    <ul class="nav nav-tabs nav-success" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="pill" href="#tab_1-0" role="tab" aria-selected="true">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-list-plus font-18 me-1'></i></div>
                                    <div class="tab-title">Service Charges</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" href="{{ url('admin/modules') }}">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-list-check font-18 me-1'></i></div>
                                    <div class="tab-title">Modules</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1-0">
                            <table id="example1" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                <thead style="font-weight: bold; font-size: 14;">
                                    <tr>
                                        <th style="width: 10px;">#</th>
                                        <th>Initial Payment</th>
                                        <th>Next Payment</th>
                                        <th>Duration</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($service_charges as $key => $charge)
                                    <tr>
                                        <td>{{ $key+1  }}</td>
                                        <td>{{ $charge->initial_pay }}</td>
                                        <td>{{ $charge->next_pay }} </td>
                                        <td>{{ $charge->duration}} </td>
                                        <td>
                                            <a  href="{{  route('service-charges.edit', $charge->id) }}">
                                                <i class="fa fa-edit"></i>
                                            </a> |
                                            <a href="{{ url('admin/service-charges/destroy', $charge->id) }}" onclick="return confirm('Are you sure you want to delete this record?.')">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.tab-pane -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection