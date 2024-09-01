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
                                    <div class="tab-title">Registrations</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="pill" href="#warning-pills-ack" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-download font-18 me-1'></i></div>
                                    <div class="tab-title">New Registration</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <hr>
                        <div class="tab-pane fade show active" id="warning-pills-home" role="tabpanel">
                            <div class="table-responsive">
                                <table id="example" class="table table-striped table-bordered display nowrap" style="width:100%; font-size: 14px;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>NAME</th>
                                            <th>REGID</th>
                                            <th>SERIAL</th>
                                            <th>TIN</th>
                                            <th>VRN</th>
                                            <th>ACKCODE</th>
                                            <th>ACKMSG</th>
                                            <th>TOKEN EXPIRES ON</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reginfos as $i => $reginfo)
                                        <tr>
                                            <td>{{$i+1}}</td>
                                            <td>
                                                <a href="{{ route('vfd-reg-infos.show', encrypt($reginfo->id))}}">{{$reginfo->name}}</a>
                                            </td>
                                            <td>{{$reginfo->regid}}</td>
                                            <td>{{$reginfo->serial}}</td>
                                            <td>{{$reginfo->tin}}</td>
                                            <td>{{$reginfo->vrn}}</td>
                                            <td>{{$reginfo->ackcode}}</td>
                                            <td>{{$reginfo->ackmsg}}</td>
                                            <td>{{\Carbon\Carbon::parse($reginfo->reg_date)->addSeconds($reginfo->expires_in)}}</td>
                                            <td>
                                                <a href="{{ url('admin/send-reg-info/'.encrypt($reginfo->id))}}"><i class="bx bx-send"></i>Re-Send Registration Request/Token Request</a> |
                                                <a href="{{ route('vfd-reg-infos.show', encrypt($reginfo->id))}}"><i class="bx bx-detail"></i></a> | 
                                                <a href="{{route('vfd-reg-infos.edit', encrypt($reginfo->id))}}"><i class="bx bx-edit"></i></a> | 
                                                <form id="delete-form-{{$i}}" method="POST" action="{{ route('vfd-reg-infos.destroy', encrypt($reginfo->id))}}" style="display: inline;"> 
                                                    @csrf
                                                    @method("DELETE")
                                                    <a href="#" onclick="confirmDelete('<?php echo $i; ?>')"><i class="bx bx-trash" style="color: red;"></i></a>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="warning-pills-ack" role="tabpanel"> 
                            <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('vfd-reg-infos.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="col-md-6">
                                    <label for="validationCustom01" class="form-label">Business Name</label>
                                    <select name="shop_id" class="form-control mb-3 select2" required>
                                        <option value="">Select Business</option>
                                        @foreach($shops as $shop)
                                        <option value="{{$shop->id}}">{{$shop->display_name}} ({{$shop->phone}})</option>
                                        @endforeach
                                    </select>
                                    <div class="valid-feedback">Looks good!</div>
                                    <div class="invalid-feedback">Please Select a business.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="validationCustom01" class="form-label">Business TIN Number</label>
                                    <input type="text" class="form-control" id="validationCustom01" placeholder="Enter your TIN Number" name="tin" data-inputmask='"mask": "999-999-999"' data-mask required>
                                    <div class="valid-feedback">Looks good!</div>
                                    <div class="invalid-feedback">Please provide your TIN number to register.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="validationCustom01" class="form-label">Certificate File</label>
                                    <input type="file" class="form-control" id="validationCustom01" name="file" required>
                                    <div class="valid-feedback">Looks good!</div>
                                    <div class="invalid-feedback">Please Select Certificate file.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="validationCustom01" class="form-label">CERTKEY</label>
                                    <input type="text" class="form-control" id="validationCustom01" placeholder="Enter your CERTKEY" name="certkey" required>
                                    <div class="valid-feedback">Looks good!</div>
                                    <div class="invalid-feedback">Please provide your certkey.</div>
                                </div> 
                                <div class="col-md-6">
                                    <label for="validationCustom01" class="form-label">Serial Number</label>
                                    <input type="text" class="form-control" id="validationCustom01" placeholder="Enter your Serial number" name="certbase" required>
                                    <div class="valid-feedback">Looks good!</div>
                                    <div class="invalid-feedback">Please provide your certificate Serial Number.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="validationCustom01" class="form-label">Cert Password</label>
                                    <input type="text" class="form-control" id="validationCustom01" placeholder="Enter your Password" name="cert_pass" required>
                                    <div class="valid-feedback">Looks good!</div>
                                    <div class="invalid-feedback">Please provide your certificate Password.</div>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary px-4 radius-30" type="submit">Save</button>
                                </div>
                            </form>
                        </div>                       
                    </div>
                </div>
            </div>
        </div>                
    </div>
    <!--end row-->
@endsection