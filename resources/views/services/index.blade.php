@extends('layouts.app')
    <script>
        function showHideForm(elem) {
            var newform = document.getElementById('new-form');
            var newbtn = document.getElementById('new-btn');
            var itemlist = document.getElementById('item-list');
            var newtitle = document.getElementById('new-title');
            var listtitle = document.getElementById('list-title');
            if (elem == 'show') {
                newform.style.display = 'block';
                newtitle.style.display = 'block';
                newbtn.style.display = 'none';
                itemlist.style.display = 'none';
                listtitle.style.display = 'none';
            }else{
                newform.style.display = 'none';
                newtitle.style.display = 'none';
                newbtn.style.display = 'block';
                itemlist.style.display = 'block';
                listtitle.style.display = 'block';
            }
        }


        function showHideDeviceForm(elem) {
            var newform = document.getElementById('new-device-form');
            var newbtn = document.getElementById('new-device-btn');
            var itemlist = document.getElementById('device-list');
            var newtitle = document.getElementById('new-device-title');
            var listtitle = document.getElementById('device-list-title');
            if (elem == 'show') {
                newform.style.display = 'block';
                newtitle.style.display = 'block';
                newbtn.style.display = 'none';
                itemlist.style.display = 'none';
                listtitle.style.display = 'none';
            }else{
                newform.style.display = 'none';
                newtitle.style.display = 'none';
                newbtn.style.display = 'block';
                itemlist.style.display = 'block';
                listtitle.style.display = 'block';
            }
        }


        function showHideGradeForm(elem) {
            var newform = document.getElementById('new-grade-form');
            var newbtn = document.getElementById('new-grade-btn');
            var itemlist = document.getElementById('grade-list');
            var newtitle = document.getElementById('new-grade-title');
            var listtitle = document.getElementById('grade-list-title');
            if (elem == 'show') {
                newform.style.display = 'block';
                newtitle.style.display = 'block';
                newbtn.style.display = 'none';
                itemlist.style.display = 'none';
                listtitle.style.display = 'none';
            }else{
                newform.style.display = 'none';
                newtitle.style.display = 'none';
                newbtn.style.display = 'block';
                itemlist.style.display = 'block';
                listtitle.style.display = 'block';
            }
        }

        function confirmDelete(id){
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

        function confirmDeleteDevice(id){

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
                document.getElementById('delete-device-form-'+id).submit();
                Swal.fire(
                  "{{trans('navmenu.deleted')}}",
                  "{{trans('navmenu.cancelled')}}",
                  'success'
                )
              }
            })
        }

        function confirmDeleteGrade(id){

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
                document.getElementById('delete-grade-form-'+id).submit();
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
        <div class="breadcrumb-title pe-3"></div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{url('home')}}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-xl-10 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
            <hr>
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-end  px-1 py-1">
                        <ul class="nav nav-tabs nav-success" role="tablist"  >
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" data-bs-toggle="tab" href="#services" role="tab" aria-selected="false">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='bx bx-list-plus font-18 me-1'></i></div>
                                        <div class="tab-title">{{trans('navmenu.services')}}</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#devices-tab" role="tab" aria-selected="false">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='bx bx-devices font-18 me-1'></i></div>
                                        <div class="tab-title">{{trans('navmenu.devices')}}</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#grades-tab" role="tab" aria-selected="false">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='bx bx-menu-alt-right font-18 me-1'></i></div>
                                        <div class="tab-title">{{trans('navmenu.grades')}}</div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content py-1">
                        <div class="tab-pane fade show active" id="services" role="tabpanel">
                            <div class="d-lg-flex align-items-center mb-4 gap-3">
                                <div class="position-relative">
                                    <h6 class="mb-0 text-uppercase" id="new-title" style="display: none;">{{trans('navmenu.add_new_service')}}</h6>
                                    <h6 class="mb-0 text-uppercase" id="list-title">{{trans('navmenu.services')}}</h6>
                                </div>
                                <div class="ms-auto">
                                    <button type="button" id="new-btn" class="btn btn-primary" onclick="showHideForm('show')"><i class="bx bxs-plus-square"></i>New Service</button>
                                </div>
                            </div>

                            <div class="p-4 border rounded" id="new-form" style="display: none;">
                                <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('services.store') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="col-md-6">
                                        <label for="validationCustom01" class="form-label">{{trans('navmenu.service')}}</label>
                                        <input type="text" class="form-control" id="validationCustom01" name="name" placeholder="{{trans('navmenu.hnt_enter_service_name')}}" required>
                                        <div class="valid-feedback">Looks good!</div>
                                        <div class="invalid-feedback">Please provide a Service name.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="validationCustom02" class="form-label">{{trans('navmenu.price')}}</label>
                                        <input type="number" step="any" class="form-control" id="validationCustom02" name="price" placeholder="{{trans('navmenu.hnt_service_price')}}" required>
                                        <div class="valid-feedback">Looks good!</div>
                                        <div class="invalid-feedback">Please provide a Service Price.</div>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="validationCustom03" class="form-label">{{trans('navmenu.description')}}</label>
                                        <input type="tel" class="form-control" id="validationCustom03" name="description" placeholder="{{trans('navmenu.hnt_service_desc')}}">
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-primary px-4 radius-30" type="submit">{{trans('navmenu.btn_save')}}</button>
                                        <button type="button" class="btn btn-warning px-4 radius-30" onclick="showHideForm('hide')">{{trans('navmenu.btn_cancel')}}</button>
                                    </div>
                                </form>
                            </div>

                            <div class="table-responsive" id="item-list">
                                <table id="example" class="table table-striped table-bordered display nowrap" style="width:100%; font-size: 14px;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{trans('navmenu.service')}}</th>
                                            <th>{{trans('navmenu.price')}}</th>
                                            <th>{{trans('navmenu.description')}}</th>
                                            <th>{{trans('navmenu.created_at')}}</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($services as $key => $service)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$service->name}}</td>
                                            <td>{{number_format($service->pivot->price)}}</td>
                                            <td>{{$service->pivot->description}}</td>
                                            <td>{{$service->pivot->created_at}}</td>
                                            <td>
                                                <a href="{{route('services.edit', encrypt($service->id))}}">
                                                    <i class="bx bx-edit" style="color: blue;"></i>
                                                </a> | 
                                                <form method="POST" action="{{route('services.destroy' , encrypt($service->id))}}" id="delete-form-{{$key}}" style="display: inline;"> 
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="javascript:;" onclick="return confirmDelete({{$key}})">
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
                        <div class="tab-pane fade" id="devices-tab" role="tabpanel">
                            <div class="d-lg-flex align-items-center mb-4 gap-3">
                                <div class="position-relative">
                                    <h6 class="mb-0 text-uppercase" id="new-device-title" style="display: none;">{{trans('navmenu.add_new_device')}}</h6>
                                    <h6 class="mb-0 text-uppercase" id="device-list-title">{{trans('navmenu.devices')}}</h6>
                                </div>
                                <div class="ms-auto">
                                    <button type="button" id="new-device-btn" class="btn btn-primary" onclick="showHideDeviceForm('show')"><i class="bx bxs-plus-square"></i>New Device</button>
                                </div>
                            </div>
                            <div class="p-4 border rounded" id="new-device-form" style="display: none;">
                                <form class="form row g-3" method="POST" action="{{route('devices.store')}}">
                                    @csrf
                                    <div class="col-md-4">
                                        <label class="form-label">{{trans('navmenu.device_number')}}<span style="color: red; font-weight: bold;">*</span></label>
                                        <input id="name" type="text" name="device_number" required placeholder="{{trans('navmenu.hnt_device_number')}}" class="form-control form-control-sm mb-3">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">{{trans('navmenu.device_name')}} <span style="color: red; font-weight: bold;">*</span></label>
                                        <input id="name" type="text" name="device_name" required placeholder="{{trans('navmenu.hnt_device_name')}}" class="form-control form-control-sm mb-3">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label">{{trans('navmenu.device_cost')}}</label>
                                        <input id="name" type="text" name="device_cost" placeholder="{{trans('navmenu.hnt_device_cost')}} (Optional)" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-success px-4 radius-30">{{trans('navmenu.btn_save')}}</button>
                                        <button type="button" class="btn btn-warning px-4 radius-30" onclick="showHideDevceForm('hide')">{{trans('navmenu.btn_cancel')}}</button>
                                    </div>
                                </form>
                            </div>
                            <div class="table-responsive" id="device-list">
                                <table id="devices" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{trans('navmenu.device_number')}}</th>
                                            <th>{{trans('navmenu.device_name')}}</th>
                                            <th>{{trans('navmenu.device_cost')}}</th>
                                            <th>{{trans('navmenu.created_at')}}</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($devices as $key => $device)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$device->device_number}}</td>
                                            <td>{{$device->device_name}}</td>
                                            <th>{{$device->device_cost}}</th>
                                            <td>{{$device->created_at}}</td>
                                            <td>
                                                <a href="{{route('devices.edit', encrypt($device->id))}}">
                                                    <i class="bx bx-edit" style="color: blue;"></i>
                                                </a> | 
                                                <form method="POST" action="{{route('devices.destroy' , encrypt($device->id))}}" id="delete-device-form-{{$key}}" style="display: inline;"> 
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="javascript:;" onclick="return confirmDeleteDevice({{$key}})">
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
                        <div class="tab-pane fade" id="grades-tab" role="tabpanel">
                            <div class="d-lg-flex align-items-center mb-4 gap-3">
                                <div class="position-relative">
                                    <h6 class="mb-0 text-uppercase" id="new-grade-title" style="display: none;">{{trans('navmenu.add_new_grade')}}</h6>
                                    <h6 class="mb-0 text-uppercase" id="grade-list-title">{{trans('navmenu.grades')}}</h6>
                                </div>
                                <div class="ms-auto">
                                    <button type="button" id="new-grade-btn" class="btn btn-primary" onclick="showHideGradeForm('show')"><i class="bx bxs-plus-square"></i>New Grade</button>
                                </div>
                            </div>
                            <div class="p-4 border rounded" id="new-grade-form" style="display: none;">
                                <form class="form row g-3" method="POST" action="{{route('grades.store')}}">
                                    @csrf
                                    <div class="col-md-6">
                                        <label class="control-label">{{trans('navmenu.grade')}} <span style="color: red; font-weight: bold;">*</span></label>
                                        <input id="name" type="text" name="name" required placeholder="{{trans('navmenu.hnt_grade_name')}}" class="form-control">
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-success px-4 radius-30">{{trans('navmenu.btn_save')}}</button>
                                        <button type="button" class="btn btn-warning px-4 radius-30" onclick="showHideGradeForm('hide')">{{trans('navmenu.btn_cancel')}}</button>
                                    </div>
                                </form>
                            </div>

                            <div class="table-responsive" id="grade-list">
                                 <table id="grades" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{trans('navmenu.grade')}}</th>
                                            <th>{{trans('navmenu.created_at')}}</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($grades as $index => $grade)
                                        <tr>
                                            <td>{{$index+1}}</td>
                                            <td>{{$grade->name}}</td>
                                            <td>{{$grade->created_at}}</td>
                                            <td>
                                                <a href="{{route('grades.edit', encrypt($grade->id))}}">
                                                    <i class="bx bx-edit" style="color: blue;"></i>
                                                </a> | 
                                                <form method="POST" action="{{route('grades.destroy' , encrypt($grade->id))}}" id="delete-grade-form-{{$index}}" style="display: inline;"> 
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="javascript:;" onclick="return confirmDeleteGrade({{$index}})">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->
@endsection