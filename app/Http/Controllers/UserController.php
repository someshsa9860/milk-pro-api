<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    function user()
    {
        return response(User::find(auth()->user()->id));
    }

    public function create(Request $request)
    {
        $location = $request->location;

        if ($request->id == null) {
            $request->validate([
                'username' => ['required', 'string', 'max:255', 'unique:admin_users'],
            ]);
        }

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'user_type' => $request->user_type,
            'status' => $request->status,
        ];
        if ($request->has('password')) {
            $data['password'] = Hash::make($request->password);
        }
        $user = User::updateOrCreate(
            ['id' => $request->id],
            $data
        );

        return response($user);
    }




    public function status($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response([
                'message' => "staff does not exist"
            ], 401);
        }

        $user->status = $user->status == 1 ? 0 : 1;
        $user->save();

        return response($user);
    }
    public function fetch($location)
    {
        $users = User::where('location', $location)->get();


        return response($users);
    }
}
