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
        <div class="col-md-3 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-3 mt-1 text-uppercase text-center">New Business Type</h6>
                    <form class="form row g-3" method="post" action="{{ route('types.store') }}" validate>
                        {{csrf_field()}}
                        <div class="col-md-12">
                            <label class="form-label">Parent Type</label>
                            <select name="business_type_id" id="btype" class="form-select form-select-sm mb-1" required>
                                <option value="">{{trans('navmenu.select_business_type')}}</option>
                                @foreach($types as $key => $type)
                                <option value="{{$type->id}}">{{$type->id}}. {{$type->type}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Business Type</label>
                            <input class="form-control form-control-sm mb-1 border-primary" type="text" name="name" placeholder="Enter Business Type" id="userinput8" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control form-control-sm mb-1" placeholder="Please Enter type- description"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Business Type in Swahili</label>
                            <input class="form-control form-control-sm mb-1 border-primary" type="text" name="name_sw" placeholder="Enter Business Type" id="userinput8">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description  in Swahili</label>
                            <textarea name="description_sw" class="form-control form-control-sm mb-1" placeholder="Please Enter type- description"></textarea>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary btn-sm">Save</button>
                            <button type="reset" class="btn btn-warning btn-sm">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-9 mx-auto">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-0 text-uppercase mb-3 mt-2 text-center">{{$title}}</h6>
                    <ul class="nav nav-tabs nav-success" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="pill" href="#tab_1-0" role="tab" aria-selected="true">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-list-check font-18 me-1'></i></div>
                                    <div class="tab-title">Businees Types</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="pill" href="#tab_1-1" role="tab" aria-selected="true">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-list-plus font-18 me-1'></i></div>
                                    <div class="tab-title">Main Types</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active table-responsive" id="tab_1-0" role="tabpanel">
                            
                        </div>
                        <div class="tab-pane fade table-responsive" id="tab_1-1" role="tabpanel">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection