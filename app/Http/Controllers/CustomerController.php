<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserData;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function create(Request $request)
    {
        
        $user = UserData::updateOrCreate(
            ['id'=>$request->id],
            [
                'route' => $request->route,
                'last_name' => $request->last_name,
                'add1' => $request->add1,
                'contact' => $request->contact,
                'amount' => $request->amount,
                'crate' => $request->crate,
                'type' => $request->type,
                'status' => $request->status,
            ]
        );

        return response($user);
    }

   

    public function status($id)  {
        $user=UserData::find($id);

        if(!$user){
            return response([
                'message'=>"staff does not exist"
            ],401);
        }

        $user->status=$user->status==1?0:1;
        $user->save();

        return response($user);

    }
    public function fetch()  {
        $users=UserData::all();
        return response($users);

    }

}
