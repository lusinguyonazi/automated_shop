@extends('layouts.app')

@section('content')
    <div class="box box-warning">
        <div class="box-body">
            <!-- Custom Tabs (Pulled to the right) -->
            <div class="nav-tabs-custom">

                <ul class="nav nav-tabs pull-right">
                    <li class="active"><a href="#tab_1-1" data-toggle="tab">{{ trans('navmenu.products') }}</a></li>
                    <li><a href="#tab_2-2" data-toggle="tab"><span class="glyphicon glyphicon-plus"></span>
                            {{ trans('navmenu.services') }} </a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1-1">
                        <table id="example3" class="table table-responsive table-striped display nowrap"
                            style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('navmenu.product_name') }}</th>
                                    <th>{{ trans('navmenu.in_stock') }}</th>
                                    <th>{{ trans('navmenu.basic_unit') }}</th>
                                    <th>{{ trans('navmenu.price') }}</th>
                                    @if ($settings->is_vat_registered)
                                        <th>{{ trans('navmenu.price') }} + {{ trans('navmenu.vat') }} </th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($prices as $index => $price)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $price->name }}</td>
                                        <td>{{ number_format($price->pivot->in_stock) }}</td>
                                        <td>{{ $price->basic_unit }}</td>
                                        <td>{{ number_format($price->pivot->price_per_unit) }}</td>
                                        @if ($settings->is_vat_registered)
                                            <td>{{ number_format($price->pivot->price_with_vat) }}</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_2-2">
                        <table id="example7" class="table table-responsive table-striped display nowrap"
                            style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('navmenu.service') }}</th>
                                    <th>{{ trans('navmenu.price') }}</th>
                                    @if ($settings->is_vat_registered)
                                        <th>{{ trans('navmenu.price') }} + {{ trans('navmenu.vat') }} </th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($serv_prices as $index => $serv_price)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $serv_price->name }}</td>
                                        <td>{{ number_format($serv_price->pivot->price) }}</td>
                                        @if ($settings->is_vat_registered)
                                            <td>{{ number_format($serv_price->pivot->price_vat) }}</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.tab-pane -->

                </div>
                <!-- /.tab-content -->
            </div>
            <!-- nav-tabs-custom -->
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
@endsection
