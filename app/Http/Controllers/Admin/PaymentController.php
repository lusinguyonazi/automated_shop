<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PaymentExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\SmsResponseLog;
use App\Models\AgentCustomer;
use App\Models\User;
use \Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Excel;

class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'isAdmin']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $page = 'Payments';
        $title = 'Payments';
        $now = Carbon::now();
        $start = null;
        $end = null;
        $start_date = null;
        $end_date = null;
        $searchTerm = $request->search_date;

        //check if user opted for date range
        $is_post_query = false;
        if (!is_null($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'] . ' 00:00:00';
            $end = $request['end_date'] . ' 23:59:59';
            $is_post_query = true;
        } else {
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }

        $duration = ['from' => date('d-m-Y', strtotime($start)), 'to' => date('d-m-Y', strtotime($end))];
        if (!empty($request->search_date)) {
            $payments = Payment::where('phone_number', 'LIKE', '%'.$request->search_date.'%')->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        }
        else{
            $payments = Payment::whereBetween('created_at', [$start, $end])->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        }

        return view('admin.payments.index', compact('payments', 'page', 'title', 'is_post_query', 'start_date', 'end_date', 'duration','searchTerm'));
    }

    public function query(Request $request)
    {
        $data = Payment::select("phone_number as name")->where("phone_number", "LIKE", "%{$request->input('query')}%")->get();
        Log::info($data);

        return response()->json($data);
    }

    public function paymentsExport(Request $request, Excel $excel, $from, $to, $searchTerm, $id)
    {
        $now = Carbon::now();
        $start = null;
        $end = null;
        $searchTerm = $searchTerm;

        //check if user opted for date range
        
        if (!is_null($from)) {
            $start = date('Y-m-d H:i:s', strtotime($from . ' 00:00:00'));
            $end = date('Y-m-d H:i:s', strtotime($to . ' 23:59:59'));
        } else {
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
        }

        if($searchTerm == 'noterm'){
            $searchTerm = '';
        }
        

        // dd($start . $end);
        $payments = Payment::where('phone_number', 'LIKE', '%' . $searchTerm . '%')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->get();

        switch ($id) {
            case 'pdf':
                return $excel->download(new PaymentExport($payments), 'all_transactions.pdf');
            case 'excel':
                return $excel->download(new PaymentExport($payments), 'all_transactions.xlsx');
            case 'csv':
                return $excel->download(new PaymentExport($payments), 'all_transactions.csv');
            default:
                break;
        }
    }

    public function autocompleteSearch(Request $request)
    {
        $query = $request->get('query');
        $data = Payment::select('phone_number as name')->where('phone_number', 'LIKE', '%' . $query . '%')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($data);
    }

    public function activatedPayments(Request $request)
    {
        $page = 'Activated Payments';
        $title = 'Activated Payments';
        $now = Carbon::now();
        $start = null;
        $end = null;
        $start_date = null;
        $end_date = null;

        //check if user opted for date range
        $is_post_query = false;

        if (!is_null($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'] . ' 00:00:00';
            $end = $request['end_date'] . ' 23:59:59';
            $is_post_query = true;
        } else {
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }

        $duration = ['from' => date('d-m-Y', strtotime($start)), 'to' => date('d-m-Y', strtotime($end))];

        $users = Payment::whereBetween('payments.created_at', [$start, $end])->whereRaw('CHAR_LENGTH(transaction_id) = 10')
            ->join('users', 'users.id', '=', 'payments.user_id')
            ->join('shops', 'shops.id', '=', 'payments.shop_id')
            ->select('shops.name as name', 'users.first_name as first_name', 'users.last_name as last_name', 'users.phone as phone', 'payments.phone_number as phone_number', 'payments.transaction_id as transaction_id', 'payments.code as code', 'payments.amount_paid as amount_paid', 'payments.period as period', 'payments.expire_date as expire_date', 'payments.is_expired as is_expired', 'payments.created_at as created_at')->orderBy('payments.created_at', 'desc')
            ->paginate(10)->withQueryString();

        return view('admin.payments.activated', compact('users', 'page', 'title', 'is_post_query', 'start_date', 'end_date', 'duration'));
    }

    public function agentActivations(Request $request)
    {
        $page = 'Payments';
        $title = 'Activations By Agents';
        $now = Carbon::now();
        $start = null;
        $end = null;
        $start_date = null;
        $end_date = null;

        //check if user opted for date range
        $is_post_query = false;

        if (!is_null($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'] . ' 00:00:00';
            $end = $request['end_date'] . ' 23:59:59';
            $is_post_query = true;
        } else {
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }

        $duration = ['from' => date('d-m-Y', strtotime($start)), 'to' => date('d-m-Y', strtotime($end))];

        $paybyagents = AgentCustomer::join('users', 'users.id', '=', 'agent_customers.user_id')->join('shop_user', 'shop_user.user_id', '=', 'users.id')
            ->join('shops', 'shops.id', '=', 'shop_user.shop_id')->join('payments', 'payments.shop_id', '=', 'shops.id')
            ->whereBetween('payments.created_at', [$start, $end])->whereRaw('CHAR_LENGTH(transaction_id) = 10')
            ->select('agent_customers.agent_id as agent_id', 'agent_customers.agent_code as agent_code', 'shops.name as shopname', 'shops.id as shopid', 'users.first_name as first_name', 'users.last_name as last_name', 'users.phone as phone', 'payments.phone_number as phone_number', 'payments.transaction_id as transaction_id', 'payments.code as code', 'payments.amount_paid as amount_paid', 'payments.period as period', 'payments.expire_date as expire_date', 'payments.is_expired as is_expired', 'payments.created_at as created_at')
            ->paginate(10)->withQueryString();

        return view('admin.payments.agent', compact('paybyagents', 'page', 'title', 'is_post_query', 'start_date', 'end_date', 'duration'));
    }


    public function activationsOnce(Request $request)
    {
        $page = 'Payments';
        $title = 'Payments';
        $now = Carbon::now();
        $start = null;
        $end = null;
        $start_date = null;
        $end_date = null;

        //check if user opted for date range
        $is_post_query = false;

        if (!is_null($request['start_date'])) {
            $start_date = $request['start_date'];
            $end_date = $request['end_date'];
            $start = $request['start_date'] . ' 00:00:00';
            $end = $request['end_date'] . ' 23:59:59';
            $is_post_query = true;
        } else {
            $start = $now->startOfMonth();
            $end = \Carbon\Carbon::now();
            $is_post_query = false;
        }

        $duration = ['from' => date('d-m-Y', strtotime($start)), 'to' => date('d-m-Y', strtotime($end))];

        $shops = Payment::whereBetween('payments.created_at', [$start, $end])
            ->whereRaw('CHAR_LENGTH(transaction_id) = 10')
            ->join('shops', 'shops.id', '=', 'payments.shop_id')
            ->join('users', 'users.id', '=', 'payments.user_id')
            ->select('shops.name as shopname', 'shops.id as shopid', 'users.first_name as first_name', 'users.last_name as last_name', 'users.phone as phone', 'payments.phone_number as phone_number', 'payments.transaction_id as transaction_id', 'payments.code as code', 'payments.amount_paid as amount_paid', 'payments.period as period', 'payments.expire_date as expire_date', 'payments.is_expired as is_expired', 'payments.created_at as created_at')->groupBy('shopid')->orderBy('payments.created_at', 'desc')
            ->paginate(10)->withQueryString();


        return view('admin.payments.once', compact('shops', 'page', 'title', 'is_post_query', 'start_date', 'end_date', 'duration'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page = 'Payments';
        $title = 'Payments';
        return view('admin.payments.new', compact('page', 'title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $api_key = 'WtUCp2KDdPNzcnCPjHhtJAxYDZl3NVuu';
        if ($request['api_key'] == $api_key) {
            $code = $this->generatePIN(6);
            // $checkcode = Payment::where('code', $code)->whereNull('expire_date')->first();

            $transaction_id = $request['transaction_id'];
            $phone_number = $request['phone_number'];
            $amount_paid = $request['amount_paid'];
            try {

                $payment = Payment::create([
                    'transaction_id' => $transaction_id,
                    'code' => $code,
                    'phone_number' => $phone_number,
                    'amount_paid' => $amount_paid,
                ]);

                // $message = 'Thibitisha%20malipo%20yako%20kwa%20SmartMauzo%20kwa%20kutumia%20Code%20hii%20:%20'.$code;

                // $api_url = 'https://gw.selcommobile.com:8443/bin/send.json?USERNAME=OTTL&PASSWORD=26072018!!&DESTADDR='.$phone_number.'&MESSAGE='.$message.'.';

                // $curl = curl_init();

                // curl_setopt_array($curl, array(
                //     CURLOPT_URL => $api_url,
                //     CURLOPT_RETURNTRANSFER => true,
                //     CURLOPT_ENCODING => "",                        
                //     CURLOPT_MAXREDIRS => 10,
                //     CURLOPT_TIMEOUT => 30000,
                //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                //     CURLOPT_CUSTOMREQUEST => "GET",
                //     // CURLOPT_POSTFIELDS => json_encode($params),
                //     CURLOPT_HTTPHEADER => array(
                //         // Set here requred headers
                //         "content-type: application/json",
                //     ),
                // ));
                // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
                // $response = curl_exec($curl);


                if ($amount_paid % 5000 == 0 || ($amount_paid - 2000) % 5000 == 0 || $amount_paid % 31000 == 0) {
                    $payment->period = "Uncategorized";
                    $payment->status = "Received";
                    $payment->save();
                    // $this->response = curl_exec($curl);
                    $this->sendSMS($code, $phone_number);
                } else if ($amount_paid == 2000) {
                    $payment->status = 'Received';
                    $payment->save();
                    $result = ['status' => 'Success', 'code' => 200, 'message' => 'The payment was created successfully'];
                    return json_encode($result);
                } else {
                    $payment->status = "Rejected";
                    $payment->save();
                    $result = ['status' => 'Failed', 'code' => 412, 'message' => 'Kiasi ulicholipia hakiendani na bei yeyote ya huduma hii. Tafadhali ingiza kiasi kilichoainishwa kwenye App yetu.'];
                    return json_encode($result);
                }

                // $err = curl_error($curl);

                // curl_close($curl);

                // $res = '';
                // if ($err) {
                //   $res =  $err;
                // } else {
                //   $res = (json_decode(json_encode($this->response), true));
                // }

                // SmsResponseLog::create([
                //         'response' => $res
                //     ]);


                $result = ['status' => 'Success', 'code' => 200, 'message' => 'The payment was created successfully'];
                return json_encode($result);
            } catch (\Illuminate\Database\QueryException $e) {
                $errorCode = $e->errorInfo[1];
                if ($errorCode = '1062') {
                    $result = ['error' => true, 'code' => $errorCode, 'message' => 'Duplicate entry Transaction Id.'];
                    return json_encode($result);
                }
            }
        } else {
            $result = ['code' => 413, 'message' => 'Wrong API key.'];
            return json_encode($result);
        }
    }


    public function sendSMS($code, $mobile)
    {
        $message = 'Thibitisha malipo yako ya SmartMauzo kwa kutumia Code hii : ' . $code;
        $numbers = [$mobile];
        $token = '8b49c1406246765709bfdbaa6b8a9232';
        $client = new \GuzzleHttp\Client();
        $url = "https://ovalbsms.co.tz/api/send-sms";
        $data = array(
            'form_params' => array(
                'username' => 'OTTL',
                'password' => 'ottl@2020',
                'sender' => 'SmartMauzo',
                'receiver' => $numbers,
                'message' => $message,
            ),
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        );
        $req = $client->post($url,  $data);

        $response = $req->getBody();

        SmsResponseLog::create([
            'response' => $response
        ]);
    }

    public function generatePIN($digits = 4)
    {
        $i = 0; //counter
        $pin = ""; //our default pin is blank.
        while ($i < $digits) {
            //generate a random number between 0 and 9.
            $pin .= mt_rand(0, 9);
            $i++;
        }
        return $pin;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $page = 'Edit Payments';
        $title = 'Edit Payment';
        $payment = Payment::find($id);

        return view('admin.payments.edit', compact('page', 'title', 'payment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $payment = Payment::find($id);
        $payment->amount_paid = $request['amount_paid'];
        $payment->created_at = $request['created_at'];
        $payment->activation_time = $request['activation_time'];
        $payment->expire_date = $request['expire_date'];
        $payment->status = $request['status'];
        $payment->is_expired = $request['is_expired'];
        $payment->save();

        return redirect('admin/payments')->with('success', 'Payment updated successful');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }



    public function search()
    {
        return view('search');
    }
}
