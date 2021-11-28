<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Twilio\Rest\Client;

class SigninController extends Controller
{
    /**
     * Sends sms to user using Twilio's programmable sms client
     * @param String $message Body of sms
     * @param Number $recipients string or array of phone number of recepient
     */
    private function sendMessage($message, $recipient)
    {
        $account_sid = 'AC2b98e2db5336ece8e87659e792e1afbb';
        $auth_token = '2bbdba55399abe14f8ecda49741b6c22';
        $twilio_number = '+13175481642';
        $client = new Client($account_sid, $auth_token);
        $client->messages->create($recipient, 
                ['from' => $twilio_number, 'body' => $message] );
    }
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return Response
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Authentication passed...
            $otp = random_int(100000, 999999);
            $user = User::where('id',Auth::user()->id)->get();
            $user[0]->otp = $otp;
            $user[0]->save();

            $this->sendMessage('Your Megason Diagnostic Clinics account was logged in. To proceed, ' . $user[0]->otp . ' is your OTP.', $user[0]->contact_number);

            return redirect()->intended('home');
        }else{
            return redirect()->back()->withErrors(['Invalid email or password']);
        }
    }
}