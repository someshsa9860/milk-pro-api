<?php

namespace App\Http\Controllers;

use App\Models\CabRequest;
use App\Models\Driver;
use App\Models\Notification;
use App\Models\User;
use Exception;
use Firebase\Auth\Token\Exception\ExpiredToken;
use Firebase\Auth\Token\Exception\InvalidSignature;
use Firebase\Auth\Token\Exception\InvalidToken;
use Firebase\Auth\Token\Exception\IssuedInTheFuture;
use Firebase\Auth\Token\Exception\UnknownKey;
use Kreait\Firebase\Factory;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;
use Kreait\Firebase\Messaging\CloudMessage;

class FirebaseController extends Controller
{





    public function sendEmail($data)
    {
        Mail::send('FCMModels.template', $data, function ($message) use (&$data) {
            $message->to($data['email'], $data['name'])->subject($data['name']);
        });
    }
    

    
    
    
    function sendFcmMessages($fcms, $data, $notification)
    {
        try {
            $messaging = (new Factory)->withServiceAccount(storage_path("app/fcm.json"))->createMessaging();
            $message = CloudMessage::new()->withHighestPossiblePriority()->withNotification($notification)->withData($data);

            $res = $messaging->sendMulticast($message, $fcms);

            return $res;
        } catch (Exception $e) {
            Log::channel('callvcal')->info("Error while sendFcmMessages " . " " . json_encode($e));

            return $e;
        }
    }

    ///withHighestPossiblePriority()-> CHANGED
    function sendFcmMessage($fcm_token, $data, $notification)
    {
        try {
            $messaging = (new Factory)->withServiceAccount(storage_path("app/fcm.json"))->createMessaging();
            $message = CloudMessage::withTarget('token', $fcm_token)->withHighestPossiblePriority()->withNotification($notification)->withData($data);

            $sent = $messaging->send($message);


            return $sent;
        } catch (Exception $e) {
            Log::channel('callvcal')->info("Error while sendFcmMessage " . " " . json_encode($e));

            return $e;
        }
    }


    function sendFcmToTopic($topic, $data, $notification)
    {
        
        try {
            $messaging = (new Factory)->withServiceAccount(storage_path("app/fcm.json"))->createMessaging();
            $message = CloudMessage::withTarget('topic', $topic)->withNotification($notification)->withData($data);

            $sent = $messaging->send($message);

            Log::channel('callvcal')->info("sendFcmToTopic sent" . " :" . json_encode($sent));

            return $sent;
        } catch (Exception $e) {
            Log::channel('callvcal')->info("Error while sendFcmMessage " . " :" . json_encode($e));
            return $e;
        }
    }

    function validateAccessToken($token)
    {
        Log::channel('callvcal')->info("validateTokenAccess start :" . date("Y/m/d H:i:s"));
        $response = Http::get("https://www.googleapis.com/oauth2/v3/tokeninfo", [
            'access_token' => $token,
        ]);

        $data = $response->json();
        Log::channel('callvcal')->info("validateTokenAccess data :" . json_encode($data) . date("Y/m/d H:i:s"));

        if (!$response->successful() || isset($data['error'])) {
            // Token verification failed
            return false;
        }

        // Token verification succeeded
        // You can access user information from $data, e.g., $data['sub'] is the user's Google ID

        // Perform additional validation or database operations here

        return true;
    }

    function validateTokenId($idToken)
    {
        Log::channel('callvcal')->info("validateTokenId start :" . date("Y/m/d H:i:s"));
        try {
            $auth = (new Factory)->withServiceAccount(storage_path("app/fcm.json"))->createAuth();

            $token = $auth->verifyIdToken($idToken);

            return true;
        } catch (InvalidArgumentException $e) {
            Log::channel('callvcal')->info("Error while InvalidArgumentException " . " ERROR" . json_encode($e));
        } catch (UnknownKey $e) {
            Log::channel('callvcal')->info("Error while UnknownKey " . " ERROR" . json_encode($e));
        } catch (IssuedInTheFuture $e) {
            Log::channel('callvcal')->info("Error while IssuedInTheFuture " . " ERROR" . json_encode($e));
        } catch (ExpiredToken $e) {
            Log::channel('callvcal')->info("Error while ExpiredToken " . " ERROR" . json_encode($e));
        } catch (InvalidSignature $e) {
            Log::channel('callvcal')->info("Error while InvalidSignature " . " ERROR" . json_encode($e));
        } catch (InvalidToken $e) {
            Log::channel('callvcal')->info("Error while InvalidToken " . " ERROR" . json_encode($e));
        }


        return false;
    }


    



    
    
    
    

    
}
