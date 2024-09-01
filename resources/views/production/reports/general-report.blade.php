@extends('layouts.prod')

@section('content')
<!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ url('/prod-home')}}"><i class="bx bx-home-alt"></i></a></li>
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
        <div class="row">
            <form class="dashform" action="{{url('general-report')}}" method="POST">
              @csrf
              <a href="#" onclick="javascript:savePdf()" class="btn btn-warning btn-sm  float-start col-md-3" style="margin: 5px;"><i class="bx bx-download"></i> Download PDF</a>

              <input type="hidden" name="start_date" id="start_input" value="">
              <input type="hidden" name="end_date" id="end_input" value="">
              <!-- Date and time range -->
              <div class="col-md-5 float-end">
                <div class="input-group">
                    <button type="button" class="btn btn-white float-end" id="reportrange">
                      <span><i class="bx bx-calendar"></i></span>
                    </button>
                  </div>
              </div>
              <!-- /.form group -->
            </form>
        </div>

  <!-- title row -->
        <div class="row">
            <div class="col-md-12">
              <div class="card radius-6">
                <div class="card-body">
                  <div id="report-pdf">
                      <div class="row">
                         <div style="text-align: center; text-transform: uppercase; color: blue; border-bottom: 2px solid #82B1FF; margin-bottom: 8px;">
                        @if(!is_null($shop->logo_location))
                        <figure>
                            <img class="invoice-logo" src="{{asset('storage/logos/'.$shop->logo_location)}}" alt="">
                        </figure>
                        @endif
                        <h6>{{$shop->name}}</h6>
                        <h5 class="title">{{trans('navmenu.gr_report')}}</h5>
                        <p> @if(app()->getLocale() == 'en'){{$duration}}@else{{$duration_sw}}@endif</p>          
                    </div>
                      </div>
                      <!-- Table row -->
                      <div class="row">
                        <div class="col-xs-12">  
                          <div class="card">
                            <div class="card-header">
                              <h5 class="card-title">{{trans('navmenu.raw_materials')}}</h5>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                              <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th style="width: 10px">#</th>
                                    <th>{{trans('navmenu.name')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.purchased')}}</th>
                                    <th>{{trans('navmenu.purchases_cost')}}</th>
                                    <th>{{trans('navmenu.used')}}</th>
                                    <th>{{trans('navmenu.damaged')}}</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  @foreach($raw_col as $key => $raw_co)
                                  <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$raw_co['name']}}</td>
                                    <td style="text-align: center;">{{$raw_co['purchased_qty']}}</td>
                                    <td>{{$raw_co['purchase_cost']}}</td>
                                    <td>{{$raw_co['used_qty']}}</td>
                                    <td>{{$raw_co['damaged']}}</td>
                                  </tr>
                                  @endforeach
                                  <tr>
                                    <td></td>
                                    <td>TOTAL</td>
                                    <td></td>
                                    <td>{{$raw_col->sum('purchase_cost')}}</td>
                                    <td></td>
                                    <td></td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-xs-12">
                          <div class="card">
                            <div class="card-header">
                              <h5 class="card-title">{{trans('navmenu.paking_materials')}}</h5>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                              <table class="table table-bordered">
                                <thead>
                                   <tr>
                                    <th style="width: 10px">#</th>
                                    <th>{{trans('navmenu.name')}}</th>
                                    <th style="text-align: center;">{{trans('navmenu.purchased')}}</th>
                                    <th>{{trans('navmenu.purchases_cost')}}</th>
                                    <th>{{trans('navmenu.used')}}</th>
                                    <th>{{trans('navmenu.damaged')}}</th>
                                  </tr>
                                </thead>
                                <tbody>
                                 @foreach($pack_col as $key => $pack_co)
                                  <tr>
                                    <td>{{$key+1}}</td>
                                    <td>{{$pack_co['name']}}</td>
                                    <td style="text-align: center;">{{$pack_co['purchased_qty']}}</td>
                                    <td>{{$pack_co['purchase_cost']}}</td>
                                    <td>{{$pack_co['used_qty']}}</td>
                                    <td>{{$pack_co['damaged']}}</td>
                                  </tr>
                                  @endforeach 
                                  <tr>
                                    <td></td>
                                    <td>TOTAL</td>
                                    <td></td>
                                    <td>{{$pack_col->sum('purchase_cost')}}</td>
                                    <td></td>
                                    <td></td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>

                      <div class="row">
                          <div class="col-12 col-lg-6 col-xl-6 mx-auto">
                              <div class="card">
                                  <div class="card-header">
                                    <h5 class="card-title">{{trans('navmenu.mros')}}</h5>
                                  </div>
                                  <div class="card-body">
                                      <div class="card-body">
                                        <table class="table table-bordered">
                                          <thead>
                                             <tr>
                                              <th style="width: 10px">#</th>
                                              <th>{{trans('navmenu.date')}}</th>
                                              <th>{{trans('navmenu.name')}}</th>
                                              <th style="text-align: center;">{{trans('navmenu.total_cost')}}</th>
                                            </tr>
                                          </thead>
                                          <tbody>
                                           @foreach($mros as $key => $mro)
                                            <tr>
                                              <td>{{$key+1}}</td>
                                              <td>{{$mro->date}}</td>
                                              <td>{{$mro->name}}</td>
                                              <td style="text-align: center;">{{$mro->total}}</td>
                                            </tr>
                                            @endforeach 
                                            <tr>
                                              <td></td>
                                              <td>TOTAL</td>
                                              <td></td>
                                              <td style="text-align: center;">{{$mros->sum('total')}}</td>
                                            </tr>
                                          </tbody>
                                        </table>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="col-12 col-lg-6 col-xl-6 mx-auto">
                              <div class="card">
                                <div class="card-header">
                                  <h5 class="card-title">{{trans('navmenu.production')}}</h5>
                                </div>
                                  <div class="card-body">
                                      <table class="table table-bordered">
                                        <thead>
                                          <tr>
                                            <th style="width: 10px">#</th>
                                            <th>{{trans('navmenu.product_name')}}</th>
                                            <th style="text-align: center;">{{trans('navmenu.quantity')}}</th>
                                          </tr>
                                        </thead>
                                        <tbody>
                                          @foreach($production as $k => $prod)
                                          <tr>
                                            <td>{{$k+1}}</td>
                                            <td>{{$prod->name}}</td>
                                            <td style="text-align: center;">{{$prod->quantity}}</td>
                                          </tr>
                                          @endforeach
                                        </tbody>
                                      </table>
                                  </div>
                              </div>
                          </div>
                          <div class="col-12 col-lg-8 col-xl-8 mx-auto">
                              <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">{{trans('navmenu.produced_products')}}</h5>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                  <table class="table table-bordered">
                                    <thead>
                                      <tr>
                                        <th >#</th>
                                        <th>{{trans('navmenu.product_name')}}</th>
                                        <th >{{trans('navmenu.quantity')}}</th>
                                        <th >{{trans('navmenu.date')}}</th>
                                        <th>{{trans('navmenu.batch_no')}}</th>
                                        <th>{{trans('navmenu.total_cost')}}</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      @foreach($production_logs as $k => $prod)
                                      <tr>
                                        <td>{{$k+1}}</td>
                                        <td>{{$prod->name}}</td>
                                        <td>{{$prod->quantity}}</td>
                                        <td>{{date("d/m/Y" , strtotime($prod->date))}}</td>
                                        <td>{{$prod->prod_batch}}</td>
                                        <td>{{($prod->cost_per_unit) *($prod->quantity )}}</td>
                                      </tr>
                                      @endforeach
                                    </tbody>
                                  </table>
                                </div>
                              </div>
                          </div>
                          <div class="col-12 col-lg-4 col-xl-4 mx-auto">
                              <div class="card">
                                  <div class="card-header">
                              <h5 class="card-title">{{trans('navmenu.production_costs')}}</h5>
                              </div>
                                <!-- /.card-header -->
                              <div class="card-body">
                                <div>
                                  <span>{{trans('navmenu.total_rm_cost')}}</span> <span> : {{number_format($total_rm_use)}}</span>
                                </div>
                                <div>
                                  <span>{{trans('navmenu.total_pm_cost')}}</span> <span> : {{number_format($total_pm_use)}}</span>
                                </div>
                                <div>
                                  <span>{{trans('navmenu.total_mro_cost')}}</span> <span> : {{number_format($mros->sum('total'))}}</span>
                                </div>
                                <div class="float-start"> 
                                  <span style="font-weight: bold;">TOTAL</span>
                                  <span style="font-weight: bold;">{{number_format($total_rm_use +$total_pm_use +$mros->sum('total') ) }}</span>
                                </div>
                              </div>
                            </div>
                          </div>
                      </div>

                      <div class="row" style="border-top: 2px solid #82B1FF;">
                        <div class="col-xs-6">
                          <span>{{Session::get('shop_name')}} reports</span>
                        </div>
                        <div class="col-xs-6">
                          <span class="float-end">Generated on: {{$reporttime}}</span>
                        </div>
                      </div>
                </div>
              </div>
            </div>
          </div>
        </div>
  </div>
</div>
@endsection

<script type="text/javascript">
    function savePdf() {
      const element = document.getElementById("report-pdf");
      var filename = "general production report";
      var opt = {
          margin:       0.5,
          filename:     filename+'.pdf',
          image:        { type: 'jpeg', quality: 0.98 },
          html2canvas:  { scale: 2 },
          jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        // New Promise-based usage:
      html2pdf().set(opt).from(element).save();
    }
</script>
