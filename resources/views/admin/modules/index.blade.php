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
        <div class="col-md-4 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="p-2 rounded">
                        <h6 class="mb-0 text-uppercase text-center">New Module</h6>
                        <form class="form row g-3" method="post" action="{{ route('modules.store') }}" validate>
                            {{csrf_field()}}
                            <div class="col-md-12">
                                <label class="form-label">Name</label>
                                <input class="form-control form-control-sm mb-1 border-primary" type="text" name="name" placeholder="Enter Module" id="userinput8" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Display Name</label>
                                <input class="form-control form-control-sm mb-1 border-primary" type="text" name="display_name" placeholder="Enter Display Name" id="userinput8" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Price</label>
                                <input class="form-control form-control-sm mb-1 border-primary" type="number" step="any" name="price" placeholder="Enter Module Price" id="userinput8" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control form-control-sm mb-1 border-primary"></textarea>
                            </div>
                            <div class="col-md-12">
                                <a href="{{ url('admin/modules') }}" class="btn btn-warning btn-sm"> Cancel</a>
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
                    <h6 class="mb-3 mt-2 text-uppercase text-center">{{$title}}</h6>
                    <ul class="nav nav-tabs nav-success" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" href="{{ url('admin/service-charges') }}">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-list-plus font-18 me-1'></i></div>
                                    <div class="tab-title">Service Charges</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="pill" href="#tab_1-0" role="tab" aria-selected="true">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-list-check font-18 me-1'></i></div>
                                    <div class="tab-title">Modules</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active table-responsive" id="tab_1-1" role="tabpanel">
                            <table id="example1" class="table table-striped display nowrap" style="width: 100%;">
                                <thead style="font-weight: bold; font-size: 14;">
                                    <tr>
                                        <th style="width: 10px;">#</th>
                                        <th>Name</th>
                                        <th>Display Name</th>
                                        <th>Price</th>
                                        <th>Duration</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($modules as $key => $charge)
                                    <tr>
                                        <td>{{ $key+1  }}</td>
                                        <td>{{ $charge->name }}</td>
                                        <td>{{$charge->display_name}}</td>
                                        <td>{{ $charge->price }} </td>
                                        <td>{{ $charge->duration}} </td>
                                        <td>{{ $charge->description }}</td>
                                        <td>
                                            <a  href="{{  route('modules.edit', $charge->id) }}">
                                                <i class="fa fa-edit"></i>
                                            </a> |
                                            <a href="{{ url('admin/modules/destroy', $charge->id) }}" onclick="return confirm('Are you sure you want to delete this record?.')">
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