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
        <div class="col-md-4 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{trans('navmenu.create_new_category')}}</h6>
            <hr/>
            <div class="card">
                <div class="card-body">
                    <form class="row g-3 form" method="POST" action="{{ route('categories.store')}}">
                        @csrf
                        <div class="col-md-12">
                            <label class="form-label">{{trans('navmenu.category_name')}} <span  style="color: red; font-weight: bold;">*</span></label>
                            <input id="name" type="text" name="name" required placeholder="{{trans('navmenu.hnt_category_name')}}" class="form-control form-control-sm mb-3">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">{{trans('navmenu.parent_cat')}}</label>
                            <select class="form-select form-select-sm mb-3" name="parent_id" style="width: 100%;">
                                <option value="">{{trans('navmenu.select_parent_cat')}}</option>
                                @foreach($categories as $key => $category)
                                <option value="{{$category->id}}">{{$category->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">{{trans('navmenu.description')}}</label>
                            <textarea name="description" placeholder="Enter Category Description" class="form-control form-control-sm mb-3"></textarea>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-success btn-sm">{{trans('navmenu.btn_save')}}</button>
                            <button type="reset" class="btn btn-info btn-sm">{{trans('navmenu.btn_reset')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-8 mx-auto ">
            <h5 class="mb-0 text-uppercase text-center">{{trans('navmenu.categories')}}</h5>
            <hr>
            <div class="card">
                <div class="card-header">
                    <a href="{{url('products')}}" class="btn btn-outline-primary col-xl-4 float-end" >{{trans('navmenu.products')}}</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive" >
                        <table id="example1" class="table table-striped display nowrap " style="width: 100%;">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>{{trans('navmenu.category_name')}}</th>
                                    <th>{{trans('navmenu.description')}}</th>
                                    <th>{{trans('navmenu.created_at')}}</th>
                                    <th>{{trans('navmenu.actions')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $index => $category)
                                <tr>
                                    <td>{{$category->id}}</td>
                                    @if(count($category->parents))
                                    <td><a href="{{ route('categories.show', $category->id)}}">{{ $category->parents->implode('-') }} <strong>-></strong> {{ $category->name }}</a></td>
                                    @else
                                    <td><a href="{{ route('categories.show', $category->id)}}">{{$category->name}}</a></td>
                                    @endif
                                    <td>{{$category->description}}</td>
                                    <td>{{$category->created_at}}</td>
                                    <td>
                                        <a href="{{ route('categories.edit', $category->id)}}"><i class="bx bx-edit" style="color: blue;"></i></a>
                                        <form action="{{route('categories.destroy' , encrypt($category->id))}}" method="POST" style=" display : inline;" id="delete-form-{{$index}}">
                                            @csrf
                                            @method('DELETE')
                                            <a href="javascript:;" onclick="return confirmDelete({{$index}})"><i class="bx bx-trash" style="color: red;"></i>
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
@endsection