<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceCharge;

class ServiceChargeController extends Controller
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
    public function index()
    {
        $page = 'Service Charges';
        $title = 'Service Charges';
        $service_charges = ServiceCharge::paginate(10);

        return view('admin.service-charges.index', compact('page', 'title', 'service_charges'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $page = "New Service Charge";
        $title = "New Service Charge";
        return view('admin.service-charges.add', compact('page', 'title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $service_charge = ServiceCharge::create([
            'initial_pay' => $request['initial_pay'],
            'next_pay' => $request['next_pay'],
            'duration' => $request['duration'],
            'type' => $request['type']
        ]);

        return redirect('admin/service-charges');
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
        $service_charge = ServiceCharge::find($id);
        $page = 'Edit Service Charge';
        $title = 'Edit Service Charge';
        return view('admin.service-charges.edit', compact('page', 'title', 'service_charge'));
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
        $service_charge = ServiceCharge::find($id);
        $service_charge->initial_pay = $request['initial_pay'];
        $service_charge->next_pay = $request['next_pay'];
        $service_charge->duration = $request['duration'];
        $service_charge->type = $request['type'];
        $service_charge->save();

        return redirect('admin/service-charges');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ServiceCharge::destroy($id);
        // $service_charge = ServiceCharge::find($id);
        // $service_charge->delete();

        return redirect('admin/service-charges');
    }
}
