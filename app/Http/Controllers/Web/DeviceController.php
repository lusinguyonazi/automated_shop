<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Shop;
use App\Models\Device;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        // return $shop;
        $device = Device::create([
            'shop_id' => $shop->id,
            'device_number' => $request['device_number'],
            'device_name' => $request['device_name'],
            'device_cost' => $request['device_cost']
        ]);

        return redirect()->back()->with('success', 'Your Device registered successfully');
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
        $device = Device::find(decrypt($id));
        $page = 'Services';
        $title = 'Edit Device info';
        $title_sw = 'Hariri Maelezo ya Kifaa';

        return view('services.edit-device', compact('page', 'title', 'title_sw', 'device'));
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
        $device = Device::find($id);
        $device->device_number = $request['device_number'];
        $device->device_name = $request['device_name'];
        $device->device_cost = $request['device_cost'];
        $device->save();

        return redirect('services')->with('success', 'Device successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $device = Device::find(decrypt($id));
        if (!is_null($device)) {
            $device->delete();
        }

        return redirect()->back()->with('success', 'Device successfully deleted');
    }
}
