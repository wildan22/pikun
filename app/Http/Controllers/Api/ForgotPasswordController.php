<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Password;
use App\User;
use Str;
use App\Mail\PasswordReset;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function forgot(Request $request) {
        $this->validate($request,[
            'email'=>'required|min:1|email',
        ]);
        $user = User::where('email',$request->email)->first();
        #print($user);
        if($user != Null){
            $tmppassword = Str::random(10);
            $data = [
                'nama'=>$user['email'],
                'password'=>$tmppassword,
                'email'=>$user['email'],
            ];
            $mailresp = Mail::to($user->email)->send(new PasswordReset($data));
            $updateuser = User::find($user->id);
            $updateuser->password = bcrypt($tmppassword);
            $updateuser->save();

            return response()->json([
                "success"=>True,
                "Message"=>"Password baru anda sudah dikirimkan melalui email"
            ],200);
            #print("Null");
        }
        else{
            print("NOT NULL");
        }
        #return response()->json(["msg" => 'Reset password link sent on your email id.']);
    }
}
