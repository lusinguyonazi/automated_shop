@extends('layouts.app')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $page }}</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">

        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-md-11 mx-auto">
            <h6 class="mb-0 text-uppercase text-center">{{ $title }}</h6>
            <hr />
            <form class="row g-3 dashform" action="{{ url('stock-taking') }}" method="POST" id="stockform">
                @csrf
                <div class="col-md-4">
                    <select name="product_id" class="form-control form-control-md">
                        @if (!is_null($product))
                            b
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @else
                            <option value="">{{ trans('navmenu.select_product') }}</option>
                        @endif
                        @foreach ($products as $prod)
                            <option value="{{ $prod->id }}">{{ $prod->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2"></div>

                <input type="hidden" name="start_date" id="start_input" value="">
                <input type="hidden" name="end_date" id="end_input" value="">

                <div class="form-group col-md-6">
                    <div class="input-group form-control-sm d-flex justify-content-end">
                        <button type="button" class="btn btn-sm btn-white btn-sm pull-right" id="reportrange">
                            <span>
                                <i class="fa fa-calendar"></i>
                            </span>
                            <i class="fa fa-caret-down"></i>
                        </button>
                    </div>
                </div>
            </form>
            <div class="card">
                <div class="card-body">
                    <div
                        style="text-align: center; text-transform: uppercase; color: blue; border-bottom: 2px solid #82B1FF; margin-bottom: 8px;">
                        @if (!is_null($shop->logo_location))
                            <figure>
                                <img class="invoice-logo" src="{{ asset('storage/logos/' . $shop->logo_location) }}"
                                    alt="">
                            </figure>
                        @endif
                        <h6>{{ $shop->name }}</h6>
                        <h5 class="title">{{ trans('navmenu.stock_purchase_report') }}</h6>
                            <p>
                                @if (app()->getLocale() == 'en')
                                    {{ $duration }}@else{{ $duration_sw }}
                                @endif
                            </p>
                    </div>
                    <div class="col-xs-12 table-responsive">
                        <table id="stocktaking" class="table table-responsive table-striped display nowrap"
                            style="width: 100%;">
                            <thead style="background:#E0E0E0;">
                                <tr>
                                    <th>#</th>
                                    <th>{{ trans('navmenu.product_name') }}</th>
                                    <th style="text-align: center;">{{ trans('navmenu.quantity') }}</th>
                                    <th style="text-align: center;">{{ trans('navmenu.unit_cost') }}</th>
                                    <th style="text-align: center;">{{ trans('navmenu.total') }}</th>
                                    <th style="text-align: center;">{{ trans('navmenu.source') }}</th>
                                    <th style="text-align: center;">{{ trans('navmenu.purchase_date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($stocks as $index => $stock)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $stock->name }}</td>
                                        <td style="text-align: center;">
                                            @if (is_numeric($stock->quantity_in) && floor($stock->quantity_in) != $stock->quantity_in)
                                                {{ $stock->quantity_in }}
                                            @else
                                                {{ number_format($stock->quantity_in) }}
                                            @endif
                                        </td>
                                        <td style="text-align: center;">{{ number_format($stock->buying_per_unit) }}</td>
                                        <td style="text-align: center;">
                                            {{ number_format($stock->quantity_in * $stock->buying_per_unit) }}</td>
                                        <td style="text-align: center;">{{ $stock->source }}</td>
                                        <td style="text-align: center;">{{ $stock->time_created }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th>{{ trans('navmenu.total') }}</th>
                                    <th></th>
                                    <th></th>
                                    <th style="text-align: center;">{{ number_format($total_buying) }}</th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


<script src="https://code.jquery.com/jquery-3.4.1.min.js"
    integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js">
</script>
