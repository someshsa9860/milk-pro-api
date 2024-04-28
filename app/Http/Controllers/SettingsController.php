<?php

namespace App\Http\Controllers;

use App\Models\HeaderFooter;
use App\Models\NewRate;
use App\Models\VType;
use Illuminate\Http\Request;

class SettingsController extends Controller
{

    public function getLocation()  {

        return apache_request_headers()['location']??apache_request_headers()['Location'];
        
    }

    public function saveHeaderFooter(Request $request)  {
        $data=$request->all();

        $returnData=[];
        foreach ($data as $key => $value) {
            array_push($returnData,HeaderFooter::updateOrCreate([
                'type'=>$key,'location'=>$this->getLocation()
            ],[
                'text'=>$value
            ]));
        }

        return response($this->getHeaderFooter($this->getLocation()));
    }


    public function getHeaderFooter($location)  {
        return HeaderFooter::where('location',$location)->get();
    }

    public function fetchHeaderFooter($location)  {
        return response(HeaderFooter::where('location',$location)->get());
    }
    public function fetchVtypes($location)  {
        return response(VType::where('location',$location)->get());
    }
    public function fetchVrates($location)  {
        return response([
            'rates'=>NewRate::where('location',$location)->get(),
            'vTypes'=>VType::where('location',$location)->get(),
        ]);
    }

    

    
}
