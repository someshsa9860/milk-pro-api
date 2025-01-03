<?php

namespace App\Http\Controllers;

use App\Models\AdminDeviceList;
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
    function userLogout(Request $request)
    {
        $user = User::find(auth()->user()->id);
        $deviceAttributes = [
            'ipAddress' => $request->ipAddress,
            'advertisementID' => $request->advertisementID,
            'androidDeviceInfoID' => $request->androidDeviceInfoID,
            'androidDeviceInfoBrand' => $request->androidDeviceInfoBrand,
            'androidDeviceInfoModel' => $request->androidDeviceInfoModel,
            'androidDeviceInfoVersion' => $request->androidDeviceInfoVersion,
            'androidDeviceInfoBaseRelease' => $request->androidDeviceInfoBaseRelease,
        ];

        // Determine the device identifier and the column to use for lookup
        $deviceIdentifier = $deviceAttributes['androidDeviceInfoID'] ?? $deviceAttributes['advertisementID'];
        $deviceColumn = isset($deviceAttributes['androidDeviceInfoID']) ? 'device_id' : 'device_ad_id';
        if ($deviceIdentifier) {
            $device = AdminDeviceList::where('admin_id', $user->id)
                ->where($deviceColumn, $deviceIdentifier)
                ->first();
            if ($device) {
                $device->update([
                    'status' => 'logged-out',
                    'last_logout_at' => now(),
                ]);
            }
        }
    }
    function validateUser(Request $request)
    {
        $user = User::find(auth()->user()->id);

        if ($user->status == 1) {
            return response(['message' => 'Blocked By Admin, please contact to admin.'], 403);
        }

        $deviceAttributes = [
            'ipAddress' => $request->ipAddress,
            'advertisementID' => $request->advertisementID,
            'androidDeviceInfoID' => $request->androidDeviceInfoID,
            'androidDeviceInfoBrand' => $request->androidDeviceInfoBrand,
            'androidDeviceInfoModel' => $request->androidDeviceInfoModel,
            'androidDeviceInfoVersion' => $request->androidDeviceInfoVersion,
            'androidDeviceInfoBaseRelease' => $request->androidDeviceInfoBaseRelease,
        ];

        // Determine the device identifier and the column to use for lookup
        $deviceIdentifier = $deviceAttributes['androidDeviceInfoID'] ?? $deviceAttributes['advertisementID'];
        $deviceColumn = isset($deviceAttributes['androidDeviceInfoID']) ? 'device_id' : 'device_ad_id';

        if ($deviceIdentifier) {
            $device = AdminDeviceList::where('admin_id', $user->id)
                ->where($deviceColumn, $deviceIdentifier)
                ->first();

            if ($device) {
                if ($device->block == 1) {
                    return response(['message' => 'Device Blocked By Admin, please contact to admin.'], 403);
                }

                



                $device->update([
                    'status' => 'logged-in',
                    'last_accessed' => now(),
                ]);
            } else {
                $count = AdminDeviceList::where('admin_id', $user->id)->where('block',0)->where('status', 'logged-in')->count();
                if ($count >= ($user->max_devices)) {
                    return response(['message' => 'Device Limit exceeded, please contact to admin.'], 403);
                }
                $fullName = "{$deviceAttributes['androidDeviceInfoBrand']} - "
                    . "{$deviceAttributes['androidDeviceInfoModel']} - "
                    . "{$deviceAttributes['androidDeviceInfoVersion']} - "
                    . "Android-{$deviceAttributes['androidDeviceInfoBaseRelease']}";
                
                AdminDeviceList::create([
                    'full_device_name' => $fullName,
                    'admin_id' => $user->id,
                    'block' => 0,
                    'ip_addresses' => $deviceAttributes['ipAddress'],
                    'device_id' => $deviceAttributes['androidDeviceInfoID'],
                    'device_ad_id' => $deviceAttributes['advertisementID'],
                    'status' => 'logged-in',
                    'last_accessed' => now(),
                    'last_logout_at' => null,
                    'last_login_at' => now(),
                    'uuid' => $deviceIdentifier,
                    'device_name' => $deviceAttributes['androidDeviceInfoBrand'],
                    'device_model' => $deviceAttributes['androidDeviceInfoModel'],
                    'session_id' => null,
                ]);
            }
        }

        return response(['user' => $user]);
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
