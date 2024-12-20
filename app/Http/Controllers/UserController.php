<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    function user()
    {

        $user = User::find(auth()->user()->id);
        if ($user->status == 1) {
            return response(['message' => 'Blocked By Admin, please contact to admin.'], 403);
        }

        return response(
            $user
        );
    }

    public function delete($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response(['message' => "User does not exists!", 401]);
        }
        $user->delete();
        return response(['message' => "User deleted successfully"]);
    }

    public function create(Request $request)
    {
        $location = $request->location;

        if ($request->id == null) {
            $request->validate([
                'username' => ['required', 'string', 'max:255', 'unique:admin_users'],
                'mobile' => ['required', 'string', 'max:255', 'unique:admin_users'],
            ]);
        }
        $role = $request->role;

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'user_type' => $request->user_type,
            'mobile' => $request->mobile,
            'status' => $request->status,
            'location_id' => auth()->user()->location_id
        ];
        if ($request->has('password')) {
            $data['password'] = Hash::make($request->password);
        }
        $user = User::updateOrCreate(
            ['id' => $request->id],
            $data
        );
        $posRoleId = DB::table('admin_roles')->where('slug', $role)->value('id');
        if (!$posRoleId) {
            $posRoleId = DB::table('admin_roles')->insertGetId([
                'name' => $role,
                'slug' => $role
            ]);
        }

        // Assign the role to the user
        DB::table('admin_role_users')->insert([
            'role_id' => $posRoleId,
            'user_id' => $user->id
        ]);

        $user->load('roles', 'permissions');

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
    public function fetch()
    {
        $users = User::where('location_id', auth()->user()->location_id)->get();


        return response($users);
    }
}
