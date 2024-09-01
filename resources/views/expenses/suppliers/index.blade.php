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
        <div class="col-md-3 mx-auto " >
            <h6 class="mb-0 text-uppercase text-center">{{trans('navmenu.add_new_supplier')}}</h6>
            <hr>
            <div class="card radius-6">
                <div class="card-body">
                    <form class="form" method="POST" action="{{route('suppliers.store')}}">
                        @csrf
                        <input type="hidden" name="supplier_for" value="Expense">
                        <div class="col-sm-12" >
                            <label for="name" class="form-label">{{trans('navmenu.supplier_name')}}</label>
                            <input type="text" name="name" class="form-control form-control-sm mb-1" required placeholder="{{trans('navmenu.hnt_supplier_name')}}">
                        </div>
                        <div class="col-sm-12">
                            <label class="form-label">{{trans('navmenu.contact_number')}}</label>
                            <input id="phone" type="tel" name="contact_no" placeholder="{{trans('navmenu.hnt_contact_number')}}  Eg. 0789XXXXXX" class="form-control form-control-sm mb-1" data-inputmask='"mask": "9999999999"' data-mask>
                        </div> 
                        <div class="col-sm-12">
                            <label class="form-label">{{trans('navmenu.email_address')}}</label>
                            <input type="text" name="email" placeholder="{{trans('navmenu.hnt_supplier_email')}}" class="form-control form-control-sm mb-1">
                        </div>  
                        <div class="col-sm-12">
                            <label class="form-label">{{trans('navmenu.address')}}</label>
                            <input type="text" name="address" placeholder="{{trans('navmenu.address')}}" class="form-control form-control-sm mb-1">
                        </div>
                        <div class="col-sm-12 pt-2">
                            <button type="submit" class="btn btn-success btn-sm">{{trans('navmenu.btn_save')}}</button>
                            <button type="reset" class="btn btn-warning btn-sm">{{trans('navmenu.btn_reset')}}</button>
                        </div>
                    </form>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>


        <div class="col-xl-9 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{trans('navmenu.supplier_list')}}</h6>
            <hr>
            <div class=" card radius-6">
                <!-- /.box-header -->
                <div class="card-body">
                    <table id="example1" class="table table-responsive mb-0" style="width: 100%;">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>{{trans('navmenu.supplier_name')}}</th>
                                <th>{{trans('navmenu.contact_number')}}.</th>
                                <th>{{trans('navmenu.email_address')}}</th>
                                <th>{{trans('navmenu.address')}}</th>
                                <th>{{trans('navmenu.created_at')}}</th>
                                <th>{{trans('navmenu.actions')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($suppliers as $i => $supplier)
                            <tr>
                                <td>{{$i+1}}</td>
                                <td><a href="{{ url('expense-account-stmt/'.encrypt($supplier->id)) }}">{{$supplier->name}}</a></td>
                                <td>{{$supplier->contact_no}}</td>
                                <td>{{$supplier->email}}</td>
                                <td>{{ $supplier->address}}</td>
                                <td>{{$supplier->created_at}}</td>
                                <td>
                                    <a href="{{route('suppliers.edit' , encrypt($supplier->id))}}">
                                        <i class="bx bx-edit" style="color: blue;"></i>
                                    </a> |
                                    <form method="POST" action="{{route('suppliers.destroy' , encrypt($supplier->id))}}" id="delete-form-{{$i}}" style="display: inline;"> 
                                        @csrf
                                        @method('DELETE')
                                        <a href="javascript:;" onclick="return confirmDelete({{$i}})">
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
@endsection


