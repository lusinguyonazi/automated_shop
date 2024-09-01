<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use Validator;
use App\Imports\CustomersImport;
use App\Models\Shop;
use App\Models\User;
use App\Models\Customer;
use App\Models\AnSale;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page = 'Customers';
        $title = 'My Customers';
        $title_sw = 'Wateja wangu';

        $shop = Shop::find(Session::get('shop_id'));
        if (!is_null($shop)) {
            $customers = Customer::where('shop_id', $shop->id)->get();
            
            $custids = array(
                ['id' => 1, 'name' => 'TIN'],
                ['id' => 2, 'name' => 'Driving License'],
                ['id' => 3, 'name' => 'Voters Number'],
                ['id' => 4, 'name' => 'Passport'],
                ['id' => 5, 'name' => 'NID'],
                ['id' => 6, 'name' => 'NIL'],
                ['id' => 7, 'name' => 'Meter No']
            );

            return view('sales.customers.index', compact('page', 'title', 'title_sw', 'customers', 'custids', 'shop'));
        }else{
            return redirect('login');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $now = \Carbon\Carbon::now();
        $customer = Customer::create([
            'shop_id' => $shop->id,
            'name' => $request['name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'postal_address' => $request['postal_address'],
            'physical_address' => $request['physical_address'],
            'street' => $request['street'],
            'tin' => $request['tin'],
            'vrn' => $request['vrn'],
            'country_code' => $request['phone_country'],
            'cust_id_type' => $request['cust_id_type'],
            'custid' => $request['custid'],
            'time_created' => $now,
            'class' => $request['class'],
        ]);
        $custno = 0;
        $max_no = Customer::where('shop_id', $shop->id)->latest()->first();
        if (!is_null($max_no)) {
            $custno = $max_no->cust_no+1;            
        }else{
            $custno = 1;
        }

        $customer->cust_no = $custno;
        $customer->save();

        $success = 'Customer was successfully registered';
        // Alert::success('Success!', $success);
        return redirect('customers')->with('success', $success);
    }


    public function createNew(Request $request)
    {

        $shop = Shop::find(Session::get('shop_id'));
        $now = \Carbon\Carbon::now();
        $customer = Customer::create([
            'shop_id' => $shop->id,
            'name' => $request['name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'postal_address' => $request['postal_address'],
            'physical_address' => $request['physical_address'],
            'street' => $request['street'],
            'tin' => $request['tin'],
            'vrn' => $request['vrn'],
            'cust_id_type' => $request['cust_id_type'],
            'custid' => $request['custid'],
            'time_created' => $now,
        ]);
        
        $custno = 0;
        $max_no = Customer::where('shop_id', $shop->id)->latest()->first();
        if (!is_null($max_no)) {
            $custno = $max_no->cust_no+1;            
        }else{
            $custno = 1;
        }

        $customer->cust_no = $custno;
        $customer->save();
        $success = 'Customer was successfully registered';

        // Alert::success('Success!', $success);
        return redirect()->back()->with('success', $success);
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
        $shop = Shop::find(Session::get('shop_id'));
        $customer = Customer::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
        if (is_null($customer)) {
            return redirect('forbiden');
        }else{
            $page = 'Edit customer';
            $title = 'Edit customer info';
            $title_sw = 'Hariri Taarifa za Mteja';
            $custids = array(
                ['id' => 1, 'name' => 'TIN'],
                ['id' => 2, 'name' => 'Driving License'],
                ['id' => 3, 'name' => 'Voters Number'],
                ['id' => 4, 'name' => 'Passport'],
                ['id' => 5, 'name' => 'NID'],
                ['id' => 6, 'name' => 'NIL'],
                ['id' => 7, 'name' => 'Meter No']
            );
            // return $countries;
            return view('sales.customers.edit', compact('page', 'title', 'title_sw', 'customer', 'custids'));
        }
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
        $customer = Customer::find(decrypt($id));
        $customer->name = $request['name'];
        $customer->phone = $request['phone'];
        $customer->email = $request['email'];
        $customer->postal_address = $request['postal_address'];
        $customer->physical_address = $request['physical_address'];
        $customer->street = $request['street'];
        $customer->tin = $request['tin'];
        $customer->vrn = $request['vrn'];
        $customer->country_code = $request['phone_country'];
        $customer->cust_id_type = $request['cust_id_type'];
        $customer->custid = $request['custid'];
        $customer->check_last_sale = $request['check_last_sale'];
        $customer->save();

        $success = 'Customer info was updated successfully';

        return redirect('customers')->with('success', $success);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $shop = Shop::find(Session::get('shop_id'));
        $customer = Customer::where('id', decrypt($id))->where('shop_id', $shop->id)->first();
        if (is_null($customer)) {
            return redirect('forbiden');
        }else{
            $sales = AnSale::where('customer_id', $customer->id)->count();
            if ($sales > 0) {
                $info = 'Customer associated with sales cannot be deleted.';
                return redirect('customers')->with('info', $info);
            }else{  
                $customer->delete();
                $success = 'Customer was successfully removed from your customer list';

                // Alert::success('Success!', $success);
                return redirect('customers')->with('success', $success);
            }
        }
    }

    public function deleteMultiple(Request $request){

        $shop = Shop::find(Session::get('shop_id'));

        $user = User::find(Session::get('user_id'));
        foreach ($request->input('id') as $key => $id) {
            $customer = Customer::where('id', $id)->where('shop_id', $shop->id)->first();
            if (is_null($customer)) {
                return redirect('forbiden');
            }else{
                $sales = AnSale::where('customer_id', $customer->id)->count();
                if ($sales == 0) {
                    $customer->delete();
                }
            }
        }
        $success = 'Customer was successfully removed from your customer list';
        return redirect('customers')->with('success', $success);
                
    }

    public function download()
    {
        return response()->download(public_path('customers.xlsx'));
    }


    public function import(Request $request) 
    {
         $rules = array(
            'file' => 'required',
        );
        $validator = Validator::make($request->all(), $rules);
        // process the form
        if ($validator->fails()) 
        {
            return \Redirect::to('customers')->withErrors($validator);
        }else{
            Excel::import(new CustomersImport, request()->file('file'));
            return redirect('customers')->with('success', 'Customers were imported successfully!');
        }
    }

}
