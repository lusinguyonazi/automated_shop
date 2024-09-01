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
        <div class="col-md-4 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
            <hr>
            <div class="card">
                <div class="card-body text-center">
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <td>{{trans('navmenu.category_name')}} :</td>
                                <td><strong>{{$category->name}}</strong></td>
                            </tr>
                            <tr>
                                <td>{{trans('navmenu.description')}} :</td>
                                <td><strong>{{$category->description}}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                    <a href="{{ route('categories.edit', $category->id)}}" class="btn btn-primary btn-sm"><i class="bx bx-edit"></i><b>{{trans('navmenu.edit_category')}}</b></a>
                </div>
            </div>
        </div>
    
        <div class="col-md-8 mx-auto">
            <div class="card ">
                <div class="card-body">
                    <div class="nav-tabs">
                        <ul class="nav nav-tabs">
                            <li class="nav-item"><a class="nav-link active" href="#product" data-bs-toggle="tab">{{trans('navmenu.cat_products')}}</a></li>
                            <li class="nav-item"><a class="nav-link" href="#add-product" data-bs-toggle="tab">{{trans('navmenu.add_products')}}</a></li>
                            <li class="nav-item"><a class="nav-link" href="#remove-product" data-bs-toggle="tab">{{trans('navmenu.remove_products')}}</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="active tab-pane" id="product">
                                <div class="table-responsive pt-2">
                                    <table id="example1" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>{{trans('navmenu.product_name')}}</th>
                                                <th>{{trans('navmenu.basic_unit')}}</th>
                                                <th>{{trans('navmenu.actions')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($cat_products as $index => $product)
                                            <tr>
                                                <td>{{$index+1}}</td>
                                                <td><a href="{{route('products.show' , $product->id)}}">{{$product->name}}</a></td>
                                                <td>{{$product->basic_unit}}</td>
                                                <td>
                                                    <a href="{{url('products.edit' , $product->id)}}">
                                                        <i class="bx bx-edit" style="color: blue;"></i>
                                                    </a>
                                                    <a href="{{url('products.destroy' , $product->id)}}" onclick="return confirm('Are you sure you want to delete this record')">
                                                        <i class="bx bx-trash" style="color: red;"></i>
                                                    </a>      
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>                
                            </div>
                            <!-- /.tab-pane -->
                            <div class="tab-pane" id="add-product">
                                <form class="form-horizontal" method="POST" action="{{url('add-product')}}">
                                    @csrf
                                    <input type="hidden" name="category_id" value="{{$category->id}}">
                                    <div class="pt-2">
                                        <label for="product_id" class="form-label">{{trans('navmenu.select_products_to_add')}}</label>
                                        <select name="product_id[]" class="form-control select2" multiple="multiple" data-placeholder="Select a State" style="width: 100%;">
                                            @foreach($products as $product)
                                            <option value="{{$product->pivot->product_id}}">{{$product->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="pt-2 pb-2">
                                        <button class="btn btn-success btn-sm">{{trans('navmenu.btn_add')}}</button>
                                    </div>
                                </form>
                              </div>
                              <!-- /.tab-pane -->

                              <!-- /.tab-pane -->
                              <div class="tab-pane" id="remove-product">
                                <form class="form" method="POST" action="{{url('remove-product')}}">
                                    @csrf
                                    <input type="hidden" name="category_id" value="{{$category->id}}">
                                    <div class="pt-2">
                                        <label class="label-control" for="product_id">{{trans('navmenu.select_products_to_remove')}}</label>
                                        <select name="product_id[]" class="form-control select2" multiple="multiple" data-placeholder="Select a State" style="width: 100%;">
                                            @foreach($cat_products as $product)
                                            <option value="{{$product->pivot->product_id}}">{{$product->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="pt-2">
                                        <button class="btn btn-warning btn-sm">{{trans('navmenu.btn_remove')}}</button>
                                        <a class="btn btn-danger btn-sm" href="{{ url('remove-all-prods-from-category/'.$category->id)}}">{{trans('navmenu.btn_remove_all')}}</a>
                                    </div>
                                  </div>
                                </form>
                              </div>
                          <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div>
                    <!-- /.nav-tabs-custom -->
                </div>
            </div>
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
@endsection
