<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function create(Request $request)
    {
        $location = $request->location;

        if ($request->id == null) {
            $request->validate([
                'username' => ['required', 'string', 'max:255', 'unique:users'],
            ]);
        }
        $user = User::updateOrCreate(
            ['id'=>$request->id],
            [
                'name' => $request->name,
                'username' => $request->username,
               
                'location' => $request->location,
                'user_type' => $request->user_type,
                'password' => $request->password,
                'status' => $request->status,
            ]
        );

        return response($user);
    }

   

    public function status($id)  {
        $user=User::find($id);

        if(!$user){
            return response([
                'message'=>"staff does not exist"
            ],401);
        }

        $user->status=$user->status==1?0:1;
        $user->save();

        return response($user);

    }
    public function fetch($location)  {
        $users=User::where('location',$location)->get();


        return response($users);

    }

}
