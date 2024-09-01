@extends('layouts.app')
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
    <div class="row">
        <div class="col-xl-10 mx-auto">
            <h6 class="mb-0 text-uppercase">{{$title}}</h6>
            <hr/>
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-pills nav-pills-warning mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="pill" href="#warning-pills-home" role="tab" aria-selected="true">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-group font-18 me-1'></i></div>
                                    <div class="tab-title">{{trans('navmenu.customers')}}</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="pill" href="#warning-pills-export" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-download font-18 me-1'></i></div>
                                    <div class="tab-title">{{trans('navmenu.export_customers')}}</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="pill" href="#warning-pills-new" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-list-plus font-18 me-1'></i></div>
                                    <div class="tab-title">{{trans('navmenu.new_customer')}}</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="pill" href="#warning-pills-import" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-import font-18 me-1'></i></div>
                                    <div class="tab-title"> {{trans('navmenu.import_customers')}}</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="warning-pills-home" role="tabpanel">
                            <div class="table-responsive">
                                <table id="del-multiple" class="table table-striped table-bordered display nowrap" style="width:100%; font-size: 14px;">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>{{trans('navmenu.customer_name')}}</th>
                                            <th>{{trans('navmenu.phone_number')}}</th>
                                            <th>{{trans('navmenu.email_address')}}</th>
                                            <th>{{trans('navmenu.postal_address')}}</th>
                                            <th>{{trans('navmenu.physical_address')}}</th>
                                            <th>{{trans('navmenu.street')}}</th>
                                            <th>{{trans('navmenu.tin')}} </th>
                                            <th>{{trans('navmenu.vrn')}} </th>
                                            <th>{{trans('navmenu.cust_id_type')}}</th>
                                            <th>{{trans('navmenu.id_number')}}</th>
                                            <th>{{trans('navmenu.created_at')}}</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($customers as $i => $customer)
                                        <tr>
                                            <td>{{$customer->id}}</td>
                                            <td>
                                                @if($shop->subscription_type_id >= 3)
                                                <a href="{{ url('customer-account-stmt/'.Crypt::encrypt($customer->id)) }}">{{$customer->name}}</a>
                                                @else
                                                {{$customer->name}}
                                                @endif
                                            </td>
                                            <td>{{$customer->phone}}</td>
                                            <td>{{$customer->email}}</td>
                                            <td>{{$customer->postal_address}}</td>
                                            <td>{{$customer->physical_address}}</td>
                                            <td>{{$customer->street}}</td>
                                            <td>{{$customer->tin}}</td>
                                            <td>{{$customer->vrn}}</td>
                                            <td>
                                                @foreach($custids as $cidt)
                                                @if($cidt['id'] == $customer->cust_id_type)
                                                {{$cidt['name']}}
                                                @endif
                                                @endforeach
                                            </td>
                                            <td>{{$customer->custid}}</td>
                                            <td>{{$customer->created_at}}</td>
                                            <td>
                                                <a href="{{route('customers.edit', encrypt($customer->id))}}"><i class="bx bx-edit"></i></a>
                                                <form id="delete-form-{{$i}}" method="POST" action="{{ route('customers.destroy', encrypt($customer->id))}}" style="display: inline;"> 
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
                            <form id="frm-example" action="{{url('delete-multiple-customers')}}" method="POST">
                                @csrf
                                <button id="submitButton" class="btn btn-danger btn-sm">{{trans('navmenu.delete_selected')}}</button>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="warning-pills-export" role="tabpanel">
                            <div class="table-responsive">
                                <table id="example2" class="table table-striped table-bordered display nowrap" style="width:100%; font-size: 14px;">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>{{trans('navmenu.customer_name')}}</th>
                                            <th>{{trans('navmenu.phone_number')}}</th>
                                            <th>{{trans('navmenu.email_address')}}</th>
                                            <th>{{trans('navmenu.postal_address')}}</th>
                                            <th>{{trans('navmenu.physical_address')}}</th>
                                            <th>{{trans('navmenu.street')}}</th>
                                            <th>{{trans('navmenu.tin')}} </th>
                                            <th>{{trans('navmenu.vrn')}} </th>
                                            <th>{{trans('navmenu.created_at')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($customers as $i => $customer)
                                        <tr>
                                            <td>{{$customer->id}}</td>
                                            <td>{{$customer->name}}</td>
                                            <td>{{$customer->phone}}</td>
                                            <td>{{$customer->email}}</td>
                                            <td>{{$customer->postal_address}}</td>
                                            <td>{{$customer->physical_address}}</td>
                                            <td>{{$customer->street}}</td>
                                            <td>{{$customer->tin}}</td>
                                            <td>{{$customer->vrn}}</td>
                                            <td>{{$customer->created_at}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div> 
                        </div>
                        <div class="tab-pane fade" id="warning-pills-new" role="tabpanel">
                            <form class="row g-3 needs-validation" novalidate method="POST" action="{{route('customers.store')}}">
                                @csrf
                                <div class="col-sm-4">
                                    <label class="form-label">{{trans('navmenu.customer_name')}} <span style="color: red; font-weight: bold;">*</span></label>
                                    <input id="name" type="text" name="name" required placeholder="{{trans('navmenu.hnt_customer_name')}}" class="form-control form-control-sm mb-3">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label">{{trans('navmenu.mobile')}} <span style="color:red">*</span></label><br>
                                    <input type="tel" class="form-control form-control-sm mb-3" id="inputPhoneNumber" name="phone" placeholder="Eg. 0789XXXXXX" value="{{old('phone')}}">
                                    <input type="hidden" name="phone_country" id="countryCode">
                                    <input type="hidden" name="dial_code" id="dialCode">
                                </div>
                                <div class="col-sm-4">
                                    <label class="form-label">{{trans('navmenu.email_address')}}</label>
                                    <input id="email" type="email" name="email" placeholder="{{trans('navmenu.hnt_customer_email')}}" class="form-control form-control-sm mb-3">
                                </div>
                                <div class="col-sm-4">
                                    <label for="address" class="form-label">{{trans('navmenu.postal_address')}}</label>
                                    <input id="address" type="text" name="postal_address" placeholder="{{trans('navmenu.hnt_postal_address')}}" class="form-control form-control-sm mb-3">
                                </div>
                                <div class="col-sm-4">
                                    <label for="address" class="form-label">{{trans('navmenu.physical_address')}}</label>
                                    <input id="address" type="text" name="physical_address" placeholder="{{trans('navmenu.hnt_physical_address')}}" class="form-control form-control-sm mb-3">
                                </div>
                                <div class="col-sm-4">
                                    <label for="address" class="form-label">{{trans('navmenu.street')}}</label>
                                    <input id="address" type="text" name="street" placeholder="{{trans('navmenu.hnt_street')}}" class="form-control form-control-sm mb-3">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">{{trans('navmenu.tin')}}</label>
                                    <input id="tin" type="text" name="tin" placeholder="{{trans('navmenu.hnt_customer_tin')}}" class="form-control form-control-sm mb-3"  data-inputmask='"mask": "999-999-999"' data-mask>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">{{trans('navmenu.vrn')}}</label>
                                    <input id="vrn" type="text" name="vrn" placeholder="{{trans('navmenu.hnt_customer_vrn')}}" class="form-control form-control-sm mb-3">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">{{trans('navmenu.cust_id_type')}}</label>
                                    <select class="form-select" name="cust_id_type">
                                        @foreach($custids as $cid)
                                        @if($cid['id'] == 6)
                                        <option value="{{$cid['id']}}" selected>{{$cid['name']}}</option>
                                        @else
                                        <option value="{{$cid['id']}}">{{$cid['name']}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">{{trans('navmenu.id_number')}}</label>
                                    <input type="text" name="custid" placeholder="{{trans('navmenu.hnt_id_number')}}" class="form-control form-control-sm mb-3">
                                </div>
                                <div class="col-sm-4">
                                    <button type="submit" class="btn btn-success" id="btn-submit">{{trans('navmenu.btn_save')}}</button>
                                    <button type="reset" class="btn btn-warning">{{trans('navmenu.btn_reset')}}</button>
                                </div>
                            </form>
                        </div>  
                        <div class="tab-pane fade" id="warning-pills-import" role="tabpanel">
                            <form class="row g-3 needs-validation" novalidate method="POST" action="{{url('import-customer')}}"  enctype="multipart/form-data">
                                @csrf
                                <div class="col-sm-6">
                                    <h3>Instruction to Import Customers</h3>
                                    <p>Please download the sample excel file below then use it to create your customers list excel file then Save it to your PC.</p>

                                    <p>Then Click  Browse to fetch your file then click Upload to import.</p>
                                </div>
                                <div class="col-sm-6">
                                    <h3>Download Sample Excel file</h3>
                                    <a href="{{url('excel-sample-customers')}}" class="btn btn-primary btn-sm"><i class="bx bx-download"></i> Download</a>
                                    <br><br>
                                    <div class="form-group">
                                        <label for="exampleInputFile" class="form-label">Choose Customers excel file</label>
                                        <input type="file" class="form-control" id="exampleInputFile" name="file" required>
                                        @if ($errors->has('file'))
                                        <span class="help-block" style="color: red;">
                                            <strong>{{ $errors->first('file') }}</strong>
                                        </span>
                                        @endif
                                    </div>
                                    <div class="form-group" style="margin-top: 5px;">
                                        <button type="submit" class="btn btn btn-success btn-sm"><i class="bx bx-upload"></i> Upload</button>
                                        <a href="{{ url('customers') }}" type="button" class="btn btn-warning btn-sm">
                                            <i class="bx bx-x"></i> Cancel
                                        </a>
                                    </div>
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