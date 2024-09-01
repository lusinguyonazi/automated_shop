@extends('layouts.app')

@section('content')

      <!-- SELECT2 EXAMPLE -->
      <div class="card radius-10">
        <div class="card-header">
          <h3 class="card-title">{{$product->name}}</h3>
        </div>
        <!-- /.box-header -->
        <div class="card-body">
          <div class="row">

            <form class="form-validate" method="POST" action="{{route('stocks.update' , encrypt($stock->id))}}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-sm-4 pt-2">
                        <label class="form-label">{{trans('navmenu.product_name')}}</label>
                        <select class="form-control select2" name="product_id" required>
                          <option value="{{$product->id}}">{{$product->name}}</option>
                            <option value="">{{trans('navmenu.select_product')}}</option>
                            @foreach($products as $product)
                            <option value="{{$product->id}}">{{$product->name}}</option>
                            @endforeach
                      </select>
                    </div>
                    <div class="col-sm-4 pt-2">
                        <label class="form-label">{{trans('navmenu.quantity')}}</label>
                        <input type="number" step="any" name="quantity_in" class="form-control" value="{{$stock->quantity_in}}">
                    </div>

                    <div class="col-sm-4 pt-2">
                        <label class="form-label">{{trans('navmenu.buying_price')}}</label>
                        <input type="number" step="any" name="buying_per_unit" class="form-control" value="{{$stock->buying_per_unit}}">
                    </div>

                    @if($settings->enable_exp_date)
                    <div class="col-sm-4 pt-2">
                        <label for="expired" class="form-label">{{trans('navmenu.exp_date')}}</label>
                        <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                         </div>
                        <input type="text" name="exp_date" placeholder="Choose Expire date yyyy-mm-dd" class="form-control" value="{{$stock->expire_date}}" placeholder="yyyy-mm-dd" onkeyup="
                                var v = this.value;
                                if (v.match(/^\d{4}$/) !== null) {
                                    this.value = v + '-';
                                } else if (v.match(/^\d{4}\-\d{2}$/) !== null) {
                                    this.value = v + '-';
                                }"
                                maxlength="10">
                        </div>
                    </div>
                    @endif
                </div>
                <!-- /.col -->
              <div class="col-sm-4 pt-2">
                <div class="form-group">
                   <button type="submit" class="btn btn btn-success">{{trans('navmenu.btn_save')}}</button>
                 <a href="javascript:history.back()" class="btn btn-warning">{{trans('navmenu.btn_cancel')}}</a>
               </div>
              </div>
            </form>
          </div>
          <!-- /.row -->
        </div>
      </div>
      <!-- /.box -->
@endsection

<link rel="stylesheet" href="../css/DatePickerX.css">

<script src="../js/DatePickerX.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function()
        {
            var $min = document.querySelector('[name="mnf_date"]'),
                $max = document.querySelector('[name="exp_date"]');

            $min.DatePickerX.init({
                mondayFirst: true,
                // minDate    : new Date(),
                format     : 'yyyy-mm-dd',
                maxDate    : $max
            });

            $max.DatePickerX.init({
                mondayFirst: true,
                format     : 'yyyy-mm-dd',
                minDate    : $min,
                // maxDate    : new Date()
            });

        });
    </script>