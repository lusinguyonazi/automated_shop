@extends('layouts.app')
@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3"></div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{url('home')}}"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item"><a href="{{url('categories')}}">{{trans('navmenu.categories')}}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$title}}</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-8 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
            <hr>
            <div class="card">
                <div class="card-body">
                     <form class="row g-3 form-validate" method="POST" action="{{ route('categories.update', $category->id)}}">
                        @csrf
                        {{ method_field('PATCH') }}
                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.category_name')}}</label>
                            <input type="text" name="name" class="form-control form-control-sm mb-3" value="{{$category->name}}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{trans('navmenu.parent_cat')}}</label>
                            <select class="form-select form-select-sm mb-3" name="parent_id" style="width: 100%;">
                                @if($category->parent)
                                <option selected="selected" value="{{$category->parent_id}}">{{$category->parent->name}}</option>
                                @endif
                                <option value="">{{trans('navmenu.select_parent_cat')}}</option>
                                @foreach($categories as $key => $cat)
                                <option value="{{$cat->id}}">{{$cat->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">{{trans('navmenu.description')}}</label>
                            <textarea name="description" class="form-control form-control-sm mb-3">{{$category->description}}</textarea>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn btn-success btn-sm">{{trans('navmenu.btn_save')}}</button>
                            <a href="javascript:history.back()" class="btn btn-warning btn-sm">{{trans('navmenu.btn_cancel')}}</a>
                        </div>
                    </form>  
                </div>
            </div>
        </div>
    </div>
@endsection