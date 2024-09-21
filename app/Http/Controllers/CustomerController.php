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
            ['user_id' => $request->user_id],
            [
                'route' => $request->route,
                'last_name' => $request->last_name,
                'add1' => $request->add1,
                'contact' => $request->contact,
                'amount' => $request->amount,
                'crate' => $request->crate,
                'type' => $request->type,
                'status' => $request->status,
                'location_id' => auth()->user()->location_id

            ]
        );

        return response($user);
    }



    public function status($id)
    {
        $user = UserData::find($id);

        if (!$user) {
            return response([
                'message' => "VSP does not exist"
            ], 401);
        }

        $user->status = $user->status == 1 ? 0 : 1;
        $user->save();

        return response($user);
    }
    public function delete($id)
    {
        $user = UserData::find($id);

        if (!$user) {
            return response([
                'message' => "VSP does not exist"
            ], 401);
        }

        $user->delete();

        return response([
            'message' => "VSP Deleted Successfully"
        ]);
    }
    public function fetch()
    {
        $users = UserData::where('location_id', auth()->user()->location_id)->get();
        return response($users);
    }
}
