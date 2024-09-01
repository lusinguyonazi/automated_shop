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
        <div class="col-md-9 mx-auto">
            <h6 class="mb-0 text-uppercase">{{$title}}</h6>
            <hr/>
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-pills nav-pills-warning mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" data-bs-toggle="pill" href="#warning-pills-home" role="tab" aria-selected="true">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-list-check font-18 me-1'></i></div>
                                    <div class="tab-title">Items</div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" data-bs-toggle="pill" href="#warning-pills-ack" role="tab" aria-selected="false">
                                <div class="d-flex align-items-center">
                                    <div class="tab-icon"><i class='bx bx-plus font-18 me-1'></i></div>
                                    <div class="tab-title">New Item</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <hr>
                        <div class="tab-pane fade show active" id="warning-pills-home" role="tabpanel">
                            <form id="frm-example" action="{{url('delete-multiple-vfd-items')}}" method="POST">
                                @csrf
                                <div class="table-responsive">
                                    <table id="del-multiple" class="table table-striped table-bordered display nowrap" style="width:100%; font-size: 14px;">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>{{trans('navmenu.description')}}</th>
                                                <th>{{trans('navmenu.price')}}</th>
                                                <th>{{trans('navmenu.created_at')}}</th>
                                                <th>{{trans('navmenu.actions')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($items as $i => $item)
                                            <tr>
                                                <td>{{$i+1}}</td>
                                                <td>{{$item->desc}}</td>
                                                <td>{{number_format($item->price)}}</td>
                                                <td>{{$item->created_at}}</td>
                                                <td>
                                                    <a href="{{route('vfd-items.edit', encrypt($item->id))}}"><i class="bx bx-edit"></i></a> | 
                                                    <form id="delete-form-{{$i}}" method="POST" action="{{ route('vfd-items.destroy', encrypt($item->id))}}" style="display: inline;"> 
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
                                <button id="submitButton" class="btn btn-danger">{{trans('navmenu.delete_selected')}}</button>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="warning-pills-ack" role="tabpanel">
                             <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('vfd-items.store') }}">
                                @csrf
                                <div class="col-md-6">
                                    <label for="validationCustom01" class="form-label">Item Description</label>
                                    <input type="text" class="form-control" id="validationCustom01" placeholder="Enter Item Description" name="desc" required>
                                    <div class="valid-feedback">Looks good!</div>
                                    <div class="invalid-feedback">Please enter description.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="validationCustom01" class="form-label">Item Price</label>
                                    <input type="text" class="form-control" id="validationCustom01" placeholder="Enter Item price" name="price">
                                    <div class="valid-feedback">Looks good!</div>
                                    <div class="invalid-feedback">Please enter description.</div>
                                </div>
                                <div class="col-md-12">
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