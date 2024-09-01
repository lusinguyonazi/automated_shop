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
                    <form class="row g-3 needs-validation" novalidate method="POST" action="{{ route('vfd-items.update', encrypt($item->id)) }}">
                        @csrf
                        {{ method_field('PATCH') }}
                        <div class="col-md-6">
                            <label for="validationCustom01" class="form-label">Item Description</label>
                            <input type="text" class="form-control" id="validationCustom01" placeholder="Enter Item Description" name="desc" value="{{$item->desc}}" required>
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback">Please enter description.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="validationCustom01" class="form-label">Item Price</label>
                            <input type="text" class="form-control" id="validationCustom01" placeholder="Enter Item price" name="price" value="{{$item->price}}">
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
    <!--end row-->
@endsection