@extends('layouts.app')

<script>
  function confirmEditItem() {
        Swal.fire({
          title: "{{trans('navmenu.are_you_sure_edit')}}",
          text: "{{trans('navmenu.will_affect')}}",
          type: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: "{{trans('navmenu.yes_update')}}",
          cancelButtonText: "{{trans('navmenu.no')}}"
        }).then((result) => {
          if (result.value) {
            document.getElementById('edititem').submit();
            Swal.fire(
              "{{trans('navmenu.updated')}}",
              "{{trans('navmenu.update_success')}}",
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
                    <li class="breadcrumb-item"><a href="{{ url('admin/home')}}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$page}}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">

        </div>
    </div>
    <!--end breadcrumb-->
    <div class="row">
        <div class="col-md-9 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{$title}}</h6>
            <hr/>
            <div class="card">
                <div class="card-body">
                    <form id="edititem" class="row g-3 form-validate" method="POST" action="{{ route('sale-items.update', encrypt($saleitem->id)) }}">
                        @csrf
                        {{ method_field('PATCH') }}
                        <input type="hidden" name="prod_detail" value="{{$prod_detail}}">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.product_name')}}</label>
                                <select class="form-select form-select-sm mb-3" name="product_id" required>
                                    <option value="{{$product->id}}">{{$product->name}}</option>
                                    <option value="">{{trans('navmenu.select_product')}}</option>
                                    @foreach($products as $product)
                                    <option value="{{$product->id}}">{{$product->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.quantity')}}</label>
                                <input type="number" step="any" name="quantity_sold" class="form-control form-control-sm mb-3" value="{{$saleitem->quantity_sold}}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.buying_per_unit')}}</label>
                                <input type="number" step="any" name="buying_per_unit" class="form-control form-control-sm mb-3" value="{{$saleitem->buying_per_unit}}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.selling_per_unit')}}</label>
                                <input type="number" step="any" name="price_per_unit" class="form-control form-control-sm mb-3" value="{{$saleitem->price_per_unit}}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.unit_discount')}}</label>
                                <input type="number" step="any" name="discount" class="form-control form-control-sm mb-3" value="{{$saleitem->discount}}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{trans('navmenu.total')}}</label>
                            <input type="number" name="" value="{{$saleitem->price}}" class="form-control form-control-sm mb-3" readonly>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">{{trans('navmenu.discount')}}</label>
                                <input type="number" step="any" name="total_discount" class="form-control form-control-sm mb-3" value="{{$saleitem->total_discount}}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Add VAT</label>
                            <select class="form-select form-select-sm mb-3" name="with_vat">
                                @if($saleitem->with_vat == 'yes')
                                <option value="yes">{{trans('navmenu.yes')}}</option>
                                <option value="no">{{trans('navmenu.no')}}</option>
                                @else
                                <option value="no">{{trans('navmenu.no')}}</option>
                                <option value="yes">{{trans('navmenu.yes')}}</option>
                                @endif
                            </select>
                        </div>
                        <!-- /.col -->
                        <div class="col-md-12">
                            <a href="#" class="btn btn btn-success" onclick="confirmEditItem()">{{trans('navmenu.btn_save')}}
                            </a>
                             <a href="javascript:history.back()" class="btn btn-warning">{{trans('navmenu.btn_cancel')}}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>                
    </div>
    <!--end row-->
@endsection