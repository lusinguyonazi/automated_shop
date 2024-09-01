@extends('layouts.app')

@section('content')

    <div class="box box-success">

        <div class="box-body">
            <table id="example3" class="table table-responsive table-striped display nowrap" style="width: 100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        @if ($pnos > 0)
                            <th>{{ trans('navmenu.product_no') }}</th>
                        @endif
                        @if ($settings->use_barcode)
                            <th>{{ trans('navmenu.barcode') }}</th>
                        @endif
                        @if ($pls > 0)
                            <th>{{ trans('navmenu.location') }}</th>
                        @endif
                        <th>{{ trans('navmenu.product_name') }}</th>
                        @if (Auth::user()->roles[0]['name'] == 'manager' ||
                                Auth::user()->can('view-stock') ||
                                Auth::user()->hasRole('storekeeper'))
                            <th>{{ trans('navmenu.in_stock') }}</th>
                        @endif
                        <th>{{ trans('navmenu.basic_unit') }}</th>
                        @if (Auth::user()->roles[0]['name'] == 'manager' ||
                                Auth::user()->can('manage-stock') ||
                                Auth::user()->hasRole('storekeeper'))
                            <th>{{ trans('navmenu.buying_per_unit') }}</th>
                        @endif
                        <th>{{ trans('navmenu.selling_per_unit') }}</th>
                        @if ($settings->is_vat_registered)
                            <th>{{ trans('navmenu.price') }} + {{ trans('navmenu.vat') }} </th>
                        @endif
                        @if ($settings->retail_with_wholesale)
                            <th>{{ trans('navmenu.wholesaleprice') }}</th>
                            @if ($settings->is_vat_registered)
                                <th>{{ trans('navmenu.wholesaleprice') }} + VAT</th>
                            @endif
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach ($prices as $index => $price)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            @if ($pnos > 0)
                                <td>{{ $price->pivot->product_no }}</td>
                            @endif
                            @if ($settings->use_barcode)
                                <td>{{ $price->pivot->barcode }}</td>
                            @endif
                            @if ($pls > 0)
                                <td style="text-align: left;">{{ $price->pivot->location }}</td>
                            @endif
                            <td>{{ $price->name }}</td>
                            @if (Auth::user()->roles[0]['name'] == 'manager' ||
                                    Auth::user()->can('view-stock') ||
                                    Auth::user()->hasRole('storekeeper'))
                                <td style="text-align: left;">{{ number_format($price->pivot->in_stock) }}</td>
                            @endif
                            <td style="text-align: left;">{{ $price->basic_unit }}</td>
                            @if (Auth::user()->roles[0]['name'] == 'manager' ||
                                    Auth::user()->can('manage-stock') ||
                                    Auth::user()->hasRole('storekeeper'))
                                <td style="text-align: left;">
                                    {{ number_format($price->pivot->buying_per_unit, 2, '.', ',') }}</td>
                            @endif
                            <td style="text-align: left;">{{ number_format($price->pivot->price_per_unit, 2, '.', ',') }}
                            </td>
                            @if ($settings->is_vat_registered)
                                <td style="text-align: left;">{{ number_format($price->pivot->price_with_vat) }}</td>
                            @endif
                            @if ($settings->retail_with_wholesale)
                                <td style="text-align: left;">{{ number_format($price->pivot->wholesale_price) }}</td>
                                @if ($settings->is_vat_registered)
                                    <td style="text-align: left;">{{ number_format($price->pivot->wholesale_price_vat) }}
                                    </td>
                                @endif
                            @endif
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    {{-- <tr>
                		<th>#</th>
                		<th>Product name</th>
                		<th>In Stock</th>
                        <th>Unit Measure</th>
                		<th>Price per unit</th>
                        @if ($settings->is_vat_registered)
                		<th>Price with VAT</th>
                        @endif
                	</tr> --}}
                </tfoot>
            </table>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
@endsection
