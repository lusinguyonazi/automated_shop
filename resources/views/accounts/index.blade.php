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

    function confirmShopDelete(id){
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
            document.getElementById('delete-form-shop-'+id).submit();
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
        <div class="col-md-3 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">User Information</h6>
            <hr/>
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-column align-items-center text-center">
                        <img src="{{ asset('assets/images/user.jpg') }}" alt="Admin" class="rounded-circle p-1 bg-primary" width="110">
                        <div class="mt-3">
                            <h4>{{Auth::user()->first_name}} {{Auth::user()->last_name}}</h4>
                            <p class="text-secondary mb-1">{{Auth::user()->roles[0]['display_name']}}</p>
                        </div>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                            <h6 class="mb-0">Mobile</h6>
                            <span class="text-secondary">{{Auth::user()->phone}}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                            <h6 class="mb-0">E-Mail</h6>
                            <span class="text-secondary">{{Auth::user()->email}}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                            <h6 class="mb-0">Shops</h6>
                            <span class="text-secondary">{{Auth::user()->shops()->count()}}</span>
                        </li>
                        <li></li>
                    </ul>
                    <div>
                        <a  href="{{route('user-profile.edit', encrypt(Auth::user()->id))}}" class="btn btn-primary col-12"><i class="bx bx-edit"></i><b> {{trans('navmenu.edit_profile')}}</b></a>
                    </div>
                    <div class="pt-2">
                        <a href="{{url('change-password')}}" class="btn btn-warning col-12"><i class="bx bx-key"></i><b> {{trans('navmenu.change_password')}}</b></a>
                    </div>
                </div>
            </div>
        </div>
        @if(Auth::user()->hasRole('manager'))
        <div class="col-md-9 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">Account Informaton</h6>
            <hr>
            <div class="card">
                 <div class="card-body">
                    <div class="d-flex align-items-end  px-1 py-1">
                        <ul class="nav nav-tabs nav-success" role="tablist"  >
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" data-bs-toggle="tab" href="#bizusers" role="tab" aria-selected="false">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='bx bx-list-plus font-18 me-1'></i></div>
                                        <div class="tab-title">{{trans('navmenu.business_users')}}</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#add-business" role="tab" aria-selected="false">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='bx bx-plus font-18 me-1'></i></div>
                                        <div class="tab-title">{{trans('navmenu.add_business')}}</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#my-businesses" role="tab" aria-selected="false">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='bx bx-list-check font-18 me-1'></i></div>
                                        <div class="tab-title">{{trans('navmenu.my_businesses')}}</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#my-payments" role="tab" aria-slected="false">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='bx bx-list-minus font-18 me-1'></i></div>
                                        <div class="tab-title">{{trans('navmenu.payment_history')}}</div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="d-flex justify-content-end">
                        {{-- <a href="#" class="btn btn-success pull-right"><i class="fa fa-user-plus"></i>{{trans('navmenu.new_seller')}}</a> --}}
                        <a href="{{route('user-profile.create')}}" class="btn btn-success pull-right"><i class="fa fa-user-plus"></i>{{trans('navmenu.new-user')}}</a>

                    </div>
                    <hr>
                    <div class="tab-content py-1">
                        <div class="tab-pane fade show active" id="bizusers" role="tabpanel">
                            <div class="table-responsive">
                                <table id="shop-users" class="table table-striped display nowrap" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{trans('navmenu.name')}}</th>
                                            <th>{{trans('navmenu.mobile')}}</th>
                                            <th>{{trans('navmenu.date_registered')}}</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $key => $user)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            @if($user->id === Auth::user()->id)
                                            <td>{{$user->first_name}} {{$user->last_name}}</td>
                                            @else
                                            <td><a href="{{route('user-profile.show', encrypt($user->id))}}">{{$user->first_name}} {{$user->last_name}}</a></td>
                                            @endif
                                            <td>{{$user->phone}}</td>
                                            <td>{{$user->created_at}}</td>
                                            <td>
                                                @if($user->roles[0]['name'] != 'manager')
                                              {{-- @if($user->hasRole('manager')) --}}
                                              <a href="{{route('user-profile.edit', encrypt($user->id))}}"><i class="bx bx-edit">Edit</i></a>
                                              <form id="delete-form-{{$key}}" method="POST" action="{{route('user-profile.destroy' , encrypt($user->id))}}" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                            </td><td>
                                                 <a href="javascript:;" onclick="return confirmDelete({{$key}})" ><i class="bx bx-trash" style="color: red;">Delete</i></a>
                                                 @endif
                                                 {{-- @if($user->roles[0]['name'] == 'manager')
   
   
                                                 <a  href="{{route('user-profile.edit', encrypt(Auth::user()->id))}}"><i class="bx bx-edit"></i><b> {{trans('navmenu.edit-profile')}}</b></a>
                                                 @endif --}}
                                               </td>
                                           </tr>
                                           @endforeach
                                              </form>
                                              
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="add-business" role="tabpanel">
                            <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('shops.store') }}" >
                                @csrf
                                <div class="col-sm-6">
                                    <label for="inputbName" class="form-label">{{trans('navmenu.business_name')}} <span style="color:red">*</span></label>
                                    <input id="shopname" type="text" name="shop_name"  data-rule="minlen:5" data-msg="{{trans('navmenu.hnt_enter_business_name')}}" class="form-control" placeholder="{{trans('navmenu.business_name')}}" value="{{old('shop_name')}}" required>

                                    <div class="valid-feedback">Looks good!</div>
                                    <div class="invalid-feedback">Please your Business name.</div>
                                </div>
                                <div class="col-sm-6">
                                    <label for="inputsubscr" class="form-label">{{trans('navmenu.subscripty_type')}} <span style="color:red">*</span></label>
                                    <div class="input-group">
                                        <select name="subscription_type_id" data-rule="required" data-msg="Please Choose your best Plan" id="stype" class="form-control" required>
                                        @foreach(App\Models\SubscriptionType::all() as $key => $stype)
                                            <option value="{{$stype->id}}">{{$stype->title}} Package</option>
                                        @endforeach
                                        </select>
                                        <span class="input-group-addon"><a href="{{url('prices')}}" title="Click here to View more Info about these versions"><i class="fa fa-info"></i></a></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label for="inputbType" class="form-label">{{trans('navmenu.business_type')}} <span style="color:red">*</span></label>
                                    <select name="business_type_id" id="btypes" class="form-control" required>
                                        <option value="">{{trans('navmenu.select_business_type')}}</option>
                                        @foreach(App\Models\BusinessType::all() as $key => $type)
                                            @if(app()->getLocale() == 'en')
                                            <option value="{{$type->id}}">{{$type->id}}. {{$type->type}}</option>
                                            @else
                                            <option value="{{$type->id}}">{{$type->id}}. {{$type->type_sw}}</option>
                                            @endif
                                        @endforeach
                                    </select>

                                    <div class="valid-feedback">Looks good!</div>
                                    <div class="invalid-feedback">Please Select business type.</div>
                                </div>
                                <div class="col-sm-6">
                                    <label for="inputSubType" class="form-label">{{trans('navmenu.business_sub_type')}} <span style="color:red">*</span></label>
                                    <select name="business_sub_type_id" class="form-control select2" id="sub-type" required>
                                        @foreach(App\Models\BusinessSubType::all() as $key => $type)
                                            <option value="{{$type->id}}">{{$type->id}}. {{$type->name}}</option>
                                        @endforeach
                                    </select>
                                    <div class="valid-feedback">Looks good!</div>
                                    <div class="invalid-feedback">Please select your Business type.</div>
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-danger">{{trans('navmenu.btn_submit')}}</button>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="my-businesses" role="tabpanel">
                            <div class="table-responsive">
                                <table id="example" class="table table-responsive table-striped display nowrap " style="width: 100%;">
                                    <thead style="font-weight: bold; font-size: 14;">
                                        <tr>
                                            <th style="width: 10px;">#</th>
                                            <th>{{trans('navmenu.business_name')}}</th>
                                            <td>{{trans('navmenu.street')}}</td>
                                            <th>{{trans('navmenu.district')}}</th>
                                            <th>{{trans('navmenu.city')}}</th>
                                            <th>{{trans('navmenu.date_registered')}}</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($shops as $index => $shop)
                                        <tr>
                                            <td>{{ $key+1  }}</td>
                                            <td><a href="{{route('shops.show' , encrypt($shop->id))}}">{{ $shop->name }}</a></td>
                                            <td>{{ $shop->street }}</td>
                                            <td>{{ $shop->district }} </td>
                                            <td>{{ $shop->city }} </td>
                                            <td>{{ $shop->created_at}} </td>
                                            <td>@if($user->roles[0]['name'] != 'manager')
                                                <form id="delete-form-shop-{{$key}}" method="POST" action="route('shops.destroy' , encrypt($user->id))" style="display: inline;">
                                                    <a href="{{url('delete-user/'.$user->id)}}" onclick="return confirmShopDelete({{$index}})" ><i class="bx bx-trash" style="color: red;"></i></a>
                                                </form>
                                                @endif
                                          </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="my-payments" role="tabpanel">
                            <div class="table-responsive">
                                <table id="example2" class="table table-responsive table-striped display nowrap " style="width: 100%;">
                                    <thead style="font-weight: bold; font-size: 14;">
                                        <tr>
                                            <th style="width: 10px;">#</th>
                                            <th>{{trans('navmenu.payment_date')}}</th>
                                            <td>{{trans('navmenu.receipt_no')}}</td>
                                            <td>{{trans('navmenu.pay_number')}}</td>
                                            <th>{{trans('navmenu.amount')}}</th>
                                            <th>{{trans('navmenu.duration')}}</th>
                                            <th>{{trans('navmenu.activated_by')}}</th>
                                            <th>{{trans('navmenu.activated_at')}}</th>
                                            <th>{{trans('navmenu.expire_date')}}</th>
                                            <th>{{trans('navmenu.is_expired')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($payments as $key => $payment)
                                        <tr>
                                            <td> {{ $key+1  }}</td>
                                            <td>{{ $payment->created_at}} </td>
                                            <td><a href="{{url('view-receipt/'.$payment->id)}}">{{ $payment->transaction_id }}</a> </td>
                                            <td> {{ $payment->phone }} </td>
                                            <td>{{ $payment->amount_paid }}</td>
                                            <td>{{ $payment->period }} </td>
                                            <td> {{ $payment->first_name}} {{ $payment->last_name}}</td>
                                            <td> {{ $payment->activation_time }}</td>
                                            <td>{{ $payment->expire_date }} </td>
                                            @if($payment->is_expired)
                                            <td>YES </td>
                                            @else
                                            <td>NO</td>
                                            @endif
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
        @endif
    </div>
@endsection