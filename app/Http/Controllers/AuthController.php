<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserActivity;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\PersonalAccessToken;
use PgSql\Lob;

class AuthController extends Controller
{
    public function test()
    {
        $auth = auth()->user();

        return response([
            'authenticated' => isset($auth)
        ]);
    }




    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'old_password' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'max:20'],
            // 'user_id' => ['required']
        ]);

        $user = User::find(auth()->user()->id);

        if (!$user) {
            return response([
                'message' => "User does not exists"
            ], 401);
        }

        if ($user->password != $request->password) {
            return response([
                'message' => "Wrong Password! Please enter correct old password or contact to admin"
            ], 401);
        }

        $user->password = $request->password;
        $user->save();

        return response($user);
    }



    public function login(Request $request)
    {

        $username = $request->email;
        $password = $request->password;
        $isEncrypted = true;


        $user = User::where('username', $username)->first();

        if (!$user) {
            return response([
                'message' => "User not found with username " . $username
            ], 401);
        }

        if (!$isEncrypted) {

            if ($user->password == $password) {

                return $this->returnUserToken($user, $request);
            }
        }else{
            if (Hash::check($password, $user->password)) {
                return $this->returnUserToken($user, $request);
            }
        }


        


        return response([
            'message' => "Wrong password " 
        ], 401);
    }

    public function returnUserToken($user, $request)
    {

        $response = [
            'token' => $user->createToken('token')->plainTextToken,
            'user' => $user,
        ];


        return response($response);

        // $res1=PersonalAccessToken::where('tokenable_id',$user->id)->get();

        // if(count($res1)==0)
        // {
        //     // PersonalAccessToken::where('tokenable_id',$user->id)->delete();
        //     return response([
        //         'token' => $user->createToken('token')->plainTextToken,
        //         'user' => $user,
        //     ]);
        // }
        // return response([
        //     'message' => "Please logout from another device and try again",
        //     // 'res'=>$res1
        // ],401);

    }

    public function logout()
    {
        if (auth()->user() == null) return response();

        $id = auth()->user()->id;

        $user = User::find($id);
        $user->status = 0;
        $user->save();

        // $res1=PersonalAccessToken::where('tokenable_id',$id)->delete();
        // $res2=PersonalAccessToken::where('tokenable_id',$id)->delete();


        return response([
            // $res1
            // ,$res2
        ]);
    }
    public function postLogout(Request $request)
    {
        if (auth()->user() == null) return response();

        $id = auth()->user()->id;

        $user = User::find($id);
        $user->status = 0;
        $user->save();

       
        

        // $res1=PersonalAccessToken::where('tokenable_id',$id)->delete();
        // $res2=PersonalAccessToken::where('tokenable_id',$id)->delete();


        return response([
            // $res1
            // ,$res2
        ]);
    }
    public function logoutAt($id)
    {


        // $res1=PersonalAccessToken::where('tokenable_id',$id)->delete();
        $user = User::find($id);
        $user->status = 0;
        $user->save();

        return response([

            // ,$res2
        ]);
    }











    protected $secret1 = 'A8@C1#03&56%14^ED@BE(EC)E1-A2+69=5F#142%5TD8hBCvA2@8A%33#41';
    protected $secret2 = '#1C@D5@48@65@#2#2@B7@7#2@79@6E@4F@30@76@C1@ED@59@5D@E7@54@C4@ED@#21@CA@BC@DD@69@D3@A7@9A@C9@60@7#2@#21#';



    public function invalid($fields)
    {




        return ($fields['secret1'] != $this->secret1) || ($fields['secret2'] != $this->secret2);
    }
}
