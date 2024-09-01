@extends('layouts.app')

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
        <div class="col-md-10 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
            <hr/>
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('expense-categories.update', encrypt($expcategory->id)) }}">
                        @csrf
                        @method('PATCH')
                        <div class="row">
                            <div class="col-sm-6">
                                <label class="form-label">{{trans('navmenu.name')}}</label>
                                <input class="form-control form-control-sm mb-3" type="text" name="name" placeholder="Enter Category name" value="{{$expcategory->name}}" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">{{trans('navmenu.description')}}</label>
                                <textarea name="description" class="form-control form-control-sm mb-3" placeholder="Enter Category Description">{{$expcategory->description}}</textarea>                                
                            </div>
                            <div class="col-md-6">
                                <label>Expenses of this Category are included in total Production Cost</label>
                                <select name="is_included_in_prod_cost" class="form-select form-select-sm mb-3">
                                    @if($expcategory->is_included_in_prod_cost)
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                    @else
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn btn-success">{{trans('navmenu.btn_save')}}</button>
                                <a href="#" onclick="showHideForm('hide')" class="btn btn-warning">{{trans('navmenu.btn_cancel')}}</a>
                            </div>
                        </div>
                    </form>                
                </div>
            </div>
        </div>
    </div>
@endsection