@extends('layouts.app')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-1">
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
        <div class="col-md-11 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
            <hr/>
            @if($shop->subscription_type_id >= 3)
            <form class="form row g-3" method="POST" action="{{url('filter-products')}}" >
                @csrf                    
                <div class="col-md-4">
                    <select name="category_id" onchange=' return this.form.submit()' class="form-select form-select-sm mb-1">
                        @if($isSearched)
                            <option>{{$searchcat->name}}</option>
                            <option value="">All Products</option>
                        @else
                            <option value="">All Products</option>
                        @endif
                        @foreach($categories as $key => $cat)
                            <option value="{{$cat->id}}">{{$cat->name}}</option>
                        @endforeach
                    </select>
                </div>
                @if($isSearched)
                    @if($searchcat->children->count() > 0)
                    <div class="col-md-4">
                        <select name="category_id" onchange='if(this.value != 0) { this.form.submit(); }' class="form-select form-select-sm mb-1">
                            <option>Select Sub Category</option>
                            @foreach($childrens as $key => $child)
                            <option value="{{$child->id}}">{{$child->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                @endif
            </form>
            @endif
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-end  px-1 py-1">
                        <ul class="nav nav-tabs nav-success" role="tablist"  >
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" data-bs-toggle="tab" href="#product_list" role="tab" aria-selected="false">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='bx bx-list-plus font-18 me-1'></i></div>
                                        <div class="tab-title">{{trans('navmenu.products')}}</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#new_product" role="tab" aria-selected="false">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='bx bx-plus font-18 me-1'></i></div>
                                        <div class="tab-title">{{trans('navmenu.new_product')}}</div>
                                    </div>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="tab" href="#import_file" role="tab" aria-selected="false">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='bx bx-import font-18 me-1'></i></div>
                                        <div class="tab-title">{{trans('navmenu.import')}}</div>
                                    </div>
                                </a>
                            </li>
                            @if(!is_null($settings) && $settings->generate_barcode)
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" data-bs-toggle="modal" href="#" data-bs-target="#barcode-modal">
                                    <div class="d-flex align-items-center">
                                        <div class="tab-icon"><i class='bx bx-barcode font-18 me-1'></i></div>
                                        <div class="tab-title">{{trans('navmenu.generate_barcode')}}</div>
                                    </div>
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                    <div class="tab-content py-1">
                        <div class="tab-pane fade show active table-responsive" id="product_list" role="tabpanel">
                            <hr>
                            <div class="table-responsive" id="item-list">
                                <table id="del-multiple" class="table table-striped table-bordered display nowrap" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>{{trans('navmenu.product_name')}}</th>
                                            <th>{{trans('navmenu.basic_unit')}}</th>
                                            <th>{{trans('navmenu.in_stock')}}</th>
                                            <th>{{trans('navmenu.price')}} ({{$currency}})</th>
                                            <th>{{trans('navmenu.date_registered')}}</th>
                                            <th>{{trans('navmenu.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($products as $index => $product)
                                        <tr>
                                            <td>{{$product->id}}</td>
                                            <td><a href="{{url('product-details/'.encrypt($product->id))}}">{{$product->name}}</a></td>
                                            <td style="text-align: center;">{{$product->basic_unit}}</td>
                                            <td style="text-align: center;">{{$shop->products()->where('product_id', $product->id)->first()->pivot->in_stock}}</td>
                                            <td style="text-align: center;">{{number_format($shop->products()->where('product_id', $product->id)->first()->pivot->price_per_unit, 2, '.', ',')}}</td>
                                            <td>{{$product->pivot->created_at}}</td>
                                            <td style="text-align: center;">
                                                <a href="{{url('edit-product/'.encrypt($product->id))}}">
                                                    <i class="bx bx-edit" style="color: blue;"></i>
                                                </a>
                                                <a href="#" onclick="confirmDelete('<?php echo encrypt($product->id); ?>')">
                                                    <i class="bx bx-trash" style="color: red;"></i>
                                                </a>      
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <form id="frm-prod" action="{{url('delete-multiple-products')}}" method="POST">
                                @csrf
                                <button id="submitButton" class="btn btn-danger">{{trans('navmenu.delete_selected')}}</button>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="new_product" role="tabpanel">
                            <hr>
                            <form class="row g-3 needs-validation" method="POST" action="{{ route('products.store') }}">
                                @csrf
                                <div class="col-sm-3">
                                    <label class="form-label">{{trans('navmenu.product_name')}} <span style="color: red; font-weight: bold;">*</span></label>
                                    <input id="name" type="text" name="name" required placeholder="{{trans('navmenu.hnt_product_name')}}" class="form-select form-select-sm mb-1">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">{{trans('navmenu.basic_unit')}} <span style="color: red; font-weight: bold;">*</span></label>
                                    <select class="form-select" name="basic_unit" required style="width: 100%;">
                                        @foreach($units as $key => $unit)
                                        <option value="{{$key}}">{{$unit}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">{{trans('navmenu.current_stock')}}</label>
                                    <input id="quantity_in" type="number" autocomplete="off" min="0" name="quantity_in" step="any" placeholder="{{trans('navmenu.hnt_current_stock')}}" class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">{{trans('navmenu.buying_per_unit')}}</label>
                                    <input id="unit_price" type="number" autocomplete="off" min="0" step="any" name="buying_per_unit" placeholder="{{trans('navmenu.hnt_buying_price')}}" class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-sm-3">  
                                    <label class="form-label">{{trans('navmenu.selling_per_unit')}}</label>
                                    <input id="unit_price" type="number" autocomplete="off" min="0" step="any" name="price_per_unit" placeholder="{{trans('navmenu.hnt_selling_price')}}" class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">{{trans('navmenu.wholesale_price')}}</label>
                                    <input id="unit_price" type="number" autocomplete="off" min="0" step="any" name="wholesale_price" placeholder="{{trans('navmenu.hnt_selling_price')}}" class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">{{trans('navmenu.expire_date')}}</label>
                                    <input type="text" name="expire_date" placeholder="{{trans('navmenu.hnt_expire_date')}}" class="result form-control form-control-sm mb-1" id="expire_date">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">{{trans('navmenu.product_no')}} </label>
                                    <input id="name" type="text" name="product_no" placeholder="{{trans('navmenu.hnt_product_no')}}" class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">{{trans('navmenu.location')}}</label>
                                    <input id="unit_price" type="text" name="location" placeholder="{{trans('navmenu.hnt_location')}} (Optional)" class="form-control form-control-sm mb-1">
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">{{trans('navmenu.description')}}</label>
                                    <textarea name="description" class="form-control form-control-sm mb-1" placeholder="{{trans('navmenu.hnt_product_desc')}}"></textarea>
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">{{trans('navmenu.barcode_label')}}</label>
                                    <input  name="barcode" class="form-control form-control-sm mb-1" placeholder="Scan/Type Barcode number." type="text" />
                                </div>
                                <div class="col-sm-3">
                                    <label class="form-label">{{trans('navmenu.product_category')}}</label>
                                    <div class="input-group">
                                        <select name="category_id" class="form-select form-select-sm mb-1">
                                            <option value="">{{trans('navmenu.select_category')}}</option>
                                            @foreach($categories as $category)
                                            <option value="{{$category->id}}">{{$category->name}}</option>
                                            @endforeach
                                        </select>
                                        <span class="input-group-btn"> 
                                            <button class="btn btn-info btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#category-modal" data-bs-backdrop="static" data-bs-keyboard="false">New
                                            </button>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success btn-sm">{{trans('navmenu.btn_save')}}</button>
                                    <button type="reset" class="btn btn-warning btn-sm">{{trans('navmenu.btn_reset')}}</button>
                                </div>  
                            </form>
                        </div>

                        <div class="tab-pane fade" id="import_file" role="tabpanel"> 
                            <hr>
                            <div class=" row">
                                <div class="col-sm-6">
                                    <form class="form" method="POST" action="{{url('import-product')}}"  enctype="multipart/form-data">
                                        @csrf
                                        <h4>Download Sample Excel file</h4>
                                        <a href="{{url('excel-sample')}}" class="btn btn-primary"><i class="bx bx-download"></i> Download</a>
                                        <br>
                                        <br>
                                        <div class="py-5">
                                        <div class="form-group">
                                            <h5 >Choose Products excel file</h5>
                                            <div class="card mx-auto">
                                                <div class="card-body">
                                                    <input id="exampleInputFile" class="form-control form-control-sm mb-1 form-control form-control-sm mb-1-sm mb-1" type="file" name="file" accept=".xlsx,.xls" required>
                                                </div>
                                            </div>
                                            @if ($errors->has('file'))
                                            <span class="help-block" style="color: red;">
                                                <strong>{{ $errors->first('file') }}</strong>
                                            </span>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn btn-success"><i class="bx bx-upload"></i> Upload</button>
                                            <a href="{{ url('products') }}" type="button" class="btn btn-warning mr-1">
                                                <i class="bx bx-x"></i>Cancel
                                            </a>
                                        </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-sm-6">
                                    <h4>Instruction to Import Products</h4>
                                    <p>Please download the sample excel file below then use it to create your products excel file then upload and save.</p>
                                    <p>The following are the meaning of the basic_unit abriviations</p>
                                    <p><b>Note: </b>Make sure your units match these abriviations to ensure importing success.</p>
                                    <ul>
                                        <li>pcs - Piece</li>
                                        <li>prs - Pair</li>
                                        <li>cts - Carton</li>
                                        <li>box - Box</li>
                                        <li>btl - Bottle</li>
                                        <li>pks - Pack </li>
                                        <li>kgs - Kilogram</li>
                                        <li>lts - Liter</li>
                                        <li>dzs - Dozen</li>
                                        <li>crs - Crete</li>
                                        <li>gls - Gallon</li>
                                        <li>mts - Meter</li>
                                        <li>set - Set</li>
                                        <li>fts - Foot</li>
                                        <li>fls - Float</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="category-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">{{trans('navmenu.new_category')}}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>   
            </div>
            <form class="form" method="POST" action="{{ route('categories.store')}}">
                <div class="modal-body row">
                    @csrf
                    <input type="hidden" name="in_products" value="1">
                    <div class="col-md-6">
                        <label class="form-label">{{trans('navmenu.category_name')}} <span  style="color: red; font-weight: bold;">*</span></label>
                        <input id="name" type="text" name="name" required placeholder="{{trans('navmenu.hnt_category_name')}}" class="form-control form-control-sm mb-1 form-control form-control-sm mb-1-sm" id="cat_name">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{trans('navmenu.parent_cat')}}</label>
                        <select class="form-control form-control-sm mb-1 form-select form-select-sm mb-1-sm" name="parent_id" style="width: 100%;" id="cat_parent_id">
                            <option value="">{{trans('navmenu.select_parent_cat')}}</option>
                                @foreach($categories as $key => $category)
                            <option value="{{$category->id}}">{{$category->name}}</option>
                                @endforeach
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">{{trans('navmenu.description')}}</label>
                        <textarea name="description" placeholder="Enter Category Description" class="form-control form-control-sm mb-1" id="cat_descrption"></textarea>
                    </div>
                </div>                    
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-sm">{{trans('navmenu.btn_save')}}</button>
                    <button type="reset" class="btn btn-warning btn-sm">{{trans('navmenu.btn_reset')}}</button>
                </div>
            </form>  
        </div>
    </div>
</div>
@endsection