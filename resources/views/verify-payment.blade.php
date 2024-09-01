@extends('layouts.app')
<style type="text/css">
    #partitioned {
        padding-left: 13px;
        letter-spacing: 30px;
        border: 1px solid blue;
        font-size: 20px;
        font-weight: bold;
        background-position: bottom;
        background-size: 70px 1px;
        background-repeat: repeat-x;
        background-position-x: 35px;
    }
</style>
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
    </div>

    <!--end breadcrumb-->

    <div class="row">

        <div class="col-md-11 mx-auto">
            <div class="row pt-2">
                <span class="col-md-3 text-primary">PLEASE ENTER YOUR CODE HERE <i class="fa fa-arrow-right"></i> </span>
                <div class="col-md-5">
                    <form class="search-form row" method="POST" action="{{ url('verify-payment') }}" validate>
                        @csrf
                        <div class="form-group col-md-9">
                            <input id="partitioned" type="text" maxlength="6" name="code" class="form-control"
                                autocomplete="off" required />
                        </div>
                        <div class="form-group col-md-3">
                            <button type="submit" name="submit" class="btn btn-danger btn-flat">SUBMIT</button>
                        </div>
                        <!-- /.input-group -->
                    </form>
                </div>
                <span class="col-md-4 text-primary"><i class="fa fa-arrow-left"></i> TAFADHALI INGIZA CODE YAKO HAPA</span>
            </div>
        </div>
        <div class="col-md-11 mx-auto">
            @if (Session::has('message'))
                <div class="alert alert-success">{{ Session::get('message') }}</div>
            @endif
            @if (Session::has('msg_error'))
                <div class="alert alert-warning">
                    <span>{{ Session::get('msg_error') }}</span>
                </div>
            @endif
            @if (Session::has('msg_error_en'))
                <div class="alert alert-warning">
                    <span>{{ Session::get('msg_error_en') }}</span>
                </div>
            @endif
        </div>
        <hr />
    </div>

    <div class="col-md-11 mx-auto">
        <div class="row pt-1 pb-3">
            @if (app()->getLocale() == 'en')
                {{-- English Payments Details --}}
                <div class="col-md-6 text-center align-items-center jsutify-content-center py-3">
                    <span class="text-secondary fs-5">
                        <i class="fa fa-thumbs-up text-red"></i>
                        Welcome! Make payment of the SmartMauzo Service
                        through SelcomPay(MasterPass QR) by using TigoPesa, M-Pesa (Namba ya Kampuni 123123),
                        AirtelMoney, HaloPesa or EzyPesa . . . <br>
                        Our Pay Number is:
                    </span>
                    <h2 class="text-center py-3"><b class="text-danger">60045358</b></h2>
                    <a href="{{ url('new-how-to-pay') }}" class="btn btn-success" style="margin-bottom: 15px;">HOW
                        TO PAY</a>
                </div>
                <div class="col-md-1 text-secondary py-5">
                    <h2>OR</h2>
                </div>
                <div class="col-md-5 py-3 text-center align-items-center justify-content-center">
                    <span class="text-secondary fs-5">Click Pay Online button to make your payment by using</span>
                    <img src="{{ asset('assets/images/vma2.png') }}" style="width: 100%; height: 120px;">
                    <p>
                        <a href="{{ url('make-payment') }}" class="btn btn-primary">Pay Now</a>
                    </p>
                </div>
                <div class="col-md-12">
                    <center>
                        <span class="text-center fs-3 text-secondary py-2" style="color: green;">Here is our Pricing
                            Plan (TZS)</span>
                    </center>
                </div>

                <div class="col-md-6 pt-4">
                    <table class="table text-center" style="background-color: #aeea00">
                        <center><span class="text-center fs-4 text-uppercase">Standard</span></center>
                        <thead>
                            <th>#</th>
                            <th>INITIAL PAYMENTS</th>
                            <th>NEXT PAYMENTS</th>
                            <th>DURATION</th>
                        </thead>
                        <tbody>
                            @foreach ($service as $key => $ser)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ number_format($ser->initial_pay) }}</td>
                                    <td>{{ number_format($ser->next_pay) }}</td>
                                    <td>{{ $ser->duration }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6 pt-4">
                    <table class="table text-center" style="background-color: #8bc34a; color: #fff;">
                        <center><span class="text-center fs-4 text-uppercase text-success">Premium</span></center>
                        <thead>
                            <th>#</th>
                            <th>AMOUNT</th>
                            <!-- <th>NEXT PAYMENTS</th> -->
                            <th>DURATION</th>
                        </thead>
                        <tbody>
                            @foreach ($pservice as $key => $ser)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ number_format($ser->initial_pay) }}</td>
                                    <!-- <td>{{ number_format($ser->next_pay) }}</td> -->
                                    <td>{{ $ser->duration }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                {{-- Swahili  Payments Details --}}
                <div class="col-md-6 text-center align-items-center jsutify-content-center py-3">
                    <span class="text-secondary fs-5">
                        <i class="fa fa-thumbs-up text-red"></i>
                        Karibu! Fanya malipo ya Huduma ya SmartMauzo
                        kupitia SelcomPay (MasterPass QR) kwa kutumia TigoPesa, M-Pesa (Namba ya Kampuni
                        123123), AirtelMoney, HaloPesa au EzyPesa..<br>
                        Kumbukumbu namba yetu ni:
                    </span>
                    <h2 class="text-center py-3"><b class="text-danger">60045358</b></h2>
                    <a href="{{ url('new-how-to-pay') }}" class="btn btn-success" style="margin-bottom: 15px;">JINSI YA
                        KULIPA</a>
                </div>
                <div class="col-md-1 text-secondary py-5">
                    <h2>AU</h2>
                </div>
                <div class="col-md-5 py-3 text-center align-items-center justify-content-center">
                    <span class="text-secondary fs-5">Bonyeza kitufe cha Lipa Sasa ili ufanye malipo yako kwa kutumia</span>
                    <img src="{{ asset('assets/images/vma2.png') }}" style="width: 100%; height: 120px;">
                    <p>
                        <a href="{{ url('make-payment') }}" class="btn btn-primary">Lipa Sasa</a>
                    </p>
                </div>
                <div class="col-md-12">
                    <center><span class="text-center fs-3 text-secondary py-2" style="color: green;">
                            Hapa ni Mpango wetu wa Bei (TZS).
                        </span></center>
                </div>

                <div class="col-md-6 pt-4">
                    <table class="table text-center" style="background-color: #aeea00">
                        <center><span class="text-center fs-4 text-uppercase">Standard</span></center>
                        <thead>
                            <th>#</th>
                            <th>MALIPO YA AWALI</th>
                            <th>MALIPO YANAYOFUATA</th>
                            <th>MUDA</th>
                        </thead>
                        <tbody>
                            @foreach ($service as $key => $ser)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ number_format($ser->initial_pay) }}</td>
                                    <td>{{ number_format($ser->next_pay) }}</td>
                                    @if ($key == 0)
                                        <td>Mwaka</td>
                                    @elseif($key == 1)
                                        <td>Miezi Sita</td>
                                    @elseif($key == 2)
                                        <td>Miezi Mitatau</td>
                                    @else
                                        <td>Mwezi</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6 pt-4">
                    <table class="table text-center" style="background-color: #8bc34a; color: #fff;">
                        <center><span class="text-center fs-4 text-uppercase text-success">Premium</span></center>
                        <thead>
                            <th>#</th>
                            <th>KIASI</th>
                            <!-- <th>MALIPO YANAYOFUATA</th> -->
                            <th>MUDA</th>
                        </thead>
                        <tbody>
                            @foreach ($pservice as $key => $ser)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ number_format($ser->initial_pay) }}</td>
                                    <!-- <td>{{ number_format($ser->next_pay) }}</td> -->
                                    @if ($key == 0)
                                        <td>Mwaka</td>
                                    @elseif($key == 1)
                                        <td>Miezi Sita</td>
                                    @elseif($key == 2)
                                        <td>Miezi Mitatau</td>
                                    @else
                                        <td>Mwezi</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    <!-- /.error-page -->
@endsection

<script type="text/javascript">
    var obj = document.getElementById('partitioned');
    obj.addEventListener('keydown', stopCarret);
    obj.addEventListener('keyup', stopCarret);

    function stopCarret() {
        if (obj.value.length > 5) {
            setCaretPosition(obj, 5);
        }
    }

    function setCaretPosition(elem, caretPos) {
        if (elem != null) {
            if (elem.createTextRange) {
                var range = elem.createTextRange();
                range.move('character', caretPos);
                range.select();
            } else {
                if (elem.selectionStart) {
                    elem.focus();
                    elem.setSelectionRange(caretPos, caretPos);
                } else
                    elem.focus();
            }
        }
    }
</script>

