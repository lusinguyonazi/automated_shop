@extends('layouts.app')

<script type="text/javascript">
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
  <div class="breadcrumb-title pe-3">@if(app()->getLocale() == 'en'){{$title}}@else{{$title_sw}}@endif</div>
  <div class="ps-3">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0 p-0">
        <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-user"></i></a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
      </ol>
    </nav>
  </div>
  {{-- <div class="ms-auto">
    <div class="btn-group">
      <button type="button" class="btn btn-primary">Settings</button>
      <button type="button" class="btn btn-primary split-bg-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"> 
        <span class="visually-hidden">Toggle Dropdown</span>
      </button>
      <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end">  <a class="dropdown-item" href="javascript:;">Action</a>
        <a class="dropdown-item" href="javascript:;">Another action</a>
        <a class="dropdown-item" href="javascript:;">Something else here</a>
        <div class="dropdown-divider"></div>  <a class="dropdown-item" href="javascript:;">Separated link</a>
      </div>
    </div>
  </div> --}}
</div>
<!--end breadcrumb-->
  <div class="row">
    <!-- form start -->
    <form class="form-horizontal" action="{{route('shops.update' , encrypt($shop->id))}}" method="POST" enctype="multipart/form-data">
      <div class="row">
		    <div class="col-md-6">
          <div class="card radius-6">
            <div class="card-header">
              <h5 class="card-title">Shop / Company / Business Info</h5>
            </div>
            <!-- /.box-header -->
              <div class="card-body">
                @csrf
                @method('PUT')
                <div class="row pt-2">
                  <label for="name" class="col-sm-4 form-lable">Name</label>

                  <div class="col-sm-8">
                    <input type="text" class="form-control" id="name" name="shop_name" value="{{$shop->name}}" placeholder="Business name" required>
                  </div>
                </div>
                <div class="row pt-2">
                  <label for="name" class="col-sm-4 form-lable">Short Description</label>

                  <div class="col-sm-8">
                    <input type="text" class="form-control" id="short_desc" name="short_desc" value="{{$shop->short_desc}}" placeholder="Short Description" required>
                  </div>
                </div>
                <div class="row pt-2">
                  <label for="tin" class="col-sm-4 form-lable">TIN</label>

                  <div class="col-sm-8">

                    <input type="text" class="form-control" id="tin" name="tin" value="{{$shop->tin}}" data-inputmask='"mask": "999-999-999"' data-mask placeholder="Optional(TIN)">
                  </div>
                </div>

                <div class="row pt-2">
                  <label for="vrn" class="col-sm-4 form-lable">VRN</label>

                  <div class="col-sm-8">
                    <input type="text" class="form-control" id="vrn" name="vrn" value="{{$shop->vrn}}" placeholder="Optional(VRN)">
                  </div>
                </div>
              </div>
          </div>
        </div>
       
        <div class="col-md-6">
          <div class="card">
            <div class="card-header ">
              <h5 class="card-title">Contact information</h5>
            </div>
            <!-- /.box-header -->              
            <div class="card-body">
                <div class="row pt-2">
                  <label for="phone" class="col-sm-4 form-lable">Telephone</label>

                  <div class="col-sm-8">
                    <input type="tel" class="form-control" id="tel" name="tel" value="{{$shop->tel}}" placeholder="Telephone number">
                  </div>
                </div>
                <div class="row pt-2">
                  <label for="mobile" class="col-sm-4 form-lable">Mobile</label>

                  <div class="col-sm-8">
                    <input type="mobile" class="form-control" id="mobile" name="mobile" value="{{$shop->mobile}}" placeholder="Mobile number">
                  </div>
                </div>
                <div class="row pt-2">
                  <label for="email" class="col-sm-4 form-lable">Email Address</label>

                  <div class="col-sm-8">
                    <input type="email" class="form-control" id="email" name="email" value="{{$shop->email}}" placeholder="Email Address">
                  </div>
                </div>
                <div class="row pt-2">
                  <label for="website" class="col-sm-4 form-lable">Website</label>

                  <div class="col-sm-8">
                    <input type="website" class="form-control" id="website" name="website" value="{{$shop->website}}" placeholder="Email Address">
                  </div>
                </div> 
              </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title">Addresses</h5>
            </div>
            <!-- /.box-header -->
              <div class="card-body">
                <div class="row pt-2">
                  <label for="street" class="col-sm-4 form-lable">Postal Address</label>

                  <div class="col-sm-8">
                    <input type="text" class="form-control" id="street" name="postal_address" value="{{$shop->postal_address}}" placeholder="Postal Address">
                  </div>
                </div>
                <div class="row pt-2">
                  <label for="street" class="col-sm-4 form-lable">Physical Address</label>

                  <div class="col-sm-8">
                    <input type="text" class="form-control" id="street" name="physical_address" value="{{$shop->physical_address}}" placeholder="Physical Address">
                  </div>
                </div>
                <div class="row pt-2">
                  <label for="street" class="col-sm-4 form-lable">Street</label>

                  <div class="col-sm-8">
                    <input type="text" class="form-control" id="street" name="street" value="{{$shop->street}}" placeholder="Street">
                  </div>
                </div>
                <div class="row pt-2">
                  <label for="district" class="col-sm-4 form-lable">District</label>

                  <div class="col-sm-8">
                    <input type="text" class="form-control" id="district" name="district" value="{{$shop->district}}" placeholder="District">
                  </div>
                </div>
                <div class="row pt-2">
                  <label for="city" class="col-sm-4 form-lable">Town / City</label>

                  <div class="col-sm-8">
                    <input type="text" class="form-control" id="city" name="city" value="{{$shop->city}}" placeholder="Town or City">
                  </div>
                </div>
                
              </div>
          </div>
        </div>

        <div class="col-md-6">
		      <!-- Horizontal Form -->
          <div class="card radius-6">           
            <div class="card-header">
              <h5 class="card-title">Shop / Company / Business Logo</h5>
            </div>
            <!-- /.box-header -->              
            <div class="card-body">
              <div class="row align-items-center">
                @if(!is_null($shop->logo_location))
                <figure>
                  <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="logo" width="100" height="90">
                </figure>
                @endif
              </div>
                
                <div class="row pt-2">
                  <label for="name" class="col-sm-4 form-lable">Logo</label>

                  <div class="col-sm-8">
                  	<label for="exampleInputFile">File input</label>
                  <input type="file" id="exampleInputFile" name="image">

                  <p class="help-block">Please upload your logo here.</p>
                  </div>
                </div>
                
              </div>
              <!-- /.box-body -->
              <div class="card-footer">
                <button type="submit" class="btn btn-info float-end">Save</button>
              </div>
              <!-- /.box-footer -->
          </div>
          <!-- /.box -->
        </div>
      </div>
    </form>
  </div>


    <div class="row">
        <h4 class="card-title">Bank Details</h4>
        <div class="col-md-5">
          <div class="card">
            <div class="card-header ">
              <h5 class="card-title">{{trans('navmenu.new_bank_account')}}</h5>
            </div>
            <form class="form-horizontal" action="{{route('bank-details.store')}}" method="POST">
            <div class="card-body">
                @csrf
                <div class="row pt-2">
                  <label for="bank_name" class="col-sm-4 form-label">Bank Name</label>
                    <div class="col-sm-8">
                    <input type="text" class="form-control" id="bank_name" name="bank_name" placeholder="Bank Name" required>
                  </div>
                </div>
                <div class="row pt-2">
                  <label for="bank_name" class="col-sm-4 form-label">Branch Name</label>
                    <div class="col-sm-8">
                    <input type="text" class="form-control" id="branch_name" name="branch_name" placeholder="Branch Name">
                  </div>
                </div>
                
                <div class="row pt-2">
                  <label for="swift_code" class="col-sm-4 form-label">Swift Code</label>
                  <div class="col-sm-8">
                    <input type="text" class="form-control" id="swift_code" name="swift_code" placeholder="Swift Code">
                  </div>
                </div>
                <div class="row pt-2">
                  <label for="account" class="col-sm-4 form-label">Account Number</label>
                  <div class="col-sm-8">
                    <input type="number" class="form-control" id="account" name="account_number" placeholder="Account Number" required>
                  </div>
                </div>

                <div class="row pt-2">
                  <label for="account" class="col-sm-4 form-label">Account Name</label>
                  <div class="col-sm-8">
                    <input type="text" class="form-control" id="account" name="account_name" placeholder="Account Name" required>
                  </div>
                </div>
                <div class="pt-2">
                  <button type="submit" class="btn btn-info float-end">Save</button>
                </div>
              </div>
            </form>
            
          </div>
        </div>

        <div class="col-md-7">
          <div class="card">
            <div class="card-header">
              <h5 class="card-title">{{trans('navmenu.bank_accounts')}}</h5>
            </div>
            <!-- /.box-header -->
            <div class="card-body">
              <table id="example1" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Bank Name</th>
                    <th>Branch Name</th>
                    <th>Swift Code</th>
                    <th>Account Number</th>
                    <th>Account Name</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($bankdetails as $key => $detail)
                  <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$detail->bank_name}}</td>
                    <td>{{$detail->branch_name}}</td>
                    <td>{{$detail->swift_code}}</td>
                    <td>{{$detail->account_number}}</td>
                    <td>{{$detail->account_name}}</td>
                    <td>
                      <a href="{{route('bank-details.edit', encrypt($detail->id))}}"><i class="bx bx-edit"></i></a> | 
                      <form method="POST" action="{{route('bank-details.destroy' , encrypt($detail->id))}}" id="delete-form-{{$key}}" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <a href="#" onclick="confirmDelete('{{$key}}')" class="text-danger" ><i class="bx bx-trash"></i></a>
                      </form>
                          
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
          <!-- /.box -->
        </div>
    </div>
@endsection