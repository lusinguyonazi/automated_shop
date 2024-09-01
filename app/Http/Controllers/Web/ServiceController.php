<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;
use App\Models\Shop;
use App\Models\Setting;
use App\Models\Service;
use App\Models\Device;
use App\Models\Grade;
use \Carbon\Carbon;

class ServiceController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
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
        $page = 'Services';
        $title = 'My Services';
        $title_sw = 'Huduma Yangu';
        $shop = Shop::find(Session::get('shop_id'));
        $services = $shop->services()->get();
        $settings = Setting::where('shop_id', $shop->id)->first();
        $devices = Device::where('shop_id', $shop->id)->get();
        $grades = Grade::where('shop_id', $shop->id)->get();

        return view('services.index', compact('page', 'title', 'title_sw', 'services', 'devices', 'grades', 'settings'));    
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
        $service = Service::where('name', $request['name'])->first();

        if (is_null($service)) {
            $service = Service::create([
                'name' => $request['name']
            ]);
        }

        $service_shop = $shop->services()->where('service_id', $service->id)->first();
        $now = Carbon::now();
        $active_for_sale = true;
        if (is_null($service_shop)) {
            $shop->services()->attach($service, ['description' => $request['description'], 'price' => $request['price'], 'active_for_sale' => $active_for_sale, 'time_created' => $now]);

            $message = 'This Service already was succesfully added to your business service list';
            return redirect()->back()->with('success', $message);
        }else{
            $message = 'This Service already exists in your business service list';
            return redirect()->back()->with('info', $message);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $serv = Service::find(decrypt($id));
        $shop = Shop::find(Session::get('shop_id'));

        $service = $shop->services()->where('service_id', $serv->id)->first();


        if (is_null($service)) {
            return redirect('forbiden');
        }else{

            $page = 'Services';
            $title = 'Service Details';
            $title_sw = 'Maelezo ya Huduma';

            return view('services.show', compact('page', 'title', 'title_sw', 'service'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $serv = Service::find(decrypt($id));
        $shop = Shop::find(Session::get('shop_id'));

        $service = $shop->services()->where('service_id', $serv->id)->first();
        
        if (is_null($service)) {
            return redirect('forbiden');
        }else{

            $page = 'Services';
            $title = 'Edit Service';
            $title_sw = 'Hariri Huduma';

            return view('services.edit', compact('page', 'title', 'title_sw', 'service',));
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
        $shop = Shop::find(Session::get('shop_id'));
        $service = Service::find(decrypt($id));
        $service->name = $request['name'];
        $service->save();

        $active_for_sale = true;
        $service_shop = $shop->services()->where('service_id', $service->id)->first();
        $service_shop->pivot->description = $request['description'];
        $service_shop->pivot->price = $request['price'];            
        $service_shop->pivot->active_for_sale = $active_for_sale;
        $service_shop->pivot->save();

        $message = 'This Service  was succesfully updated.';
        return redirect('services')->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $service  = Service::find(decrypt($id));
        $shop = Shop::find(Session::get('shop_id'));

        $service_shop = $shop->services()->where('service_id', $service->id)->first();
        if (is_null($service_shop)) {
            return redirect('forbiden');
        }else{
            $shop->services()->detach($service);
        
            $message = 'This Service  was succesfully removed.';
            return redirect('services')->with('success', $message);
        }
    }

}
