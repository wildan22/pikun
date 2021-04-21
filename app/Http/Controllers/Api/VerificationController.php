<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\VerifiesEmails;
use App\User;
use App\Mail\Mailer;
use Illuminate\Support\Facades\Mail;

class VerificationController extends Controller
{
    /*
    public function __construct() {
        $this->middleware('auth:api')->except(['verify']);
    }
    */
    public function verify(Request $request){
        $user = User::find($request->route('id'));
        //VALIDATE IF USER EXISTS
        if($user != Null){
            ///VALIDATE IF HASH AND SIGNATURE IS VALID
            if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
                return response()->json([
                    "success"=>False,
                    "Message"=>"Link Verifikasi anda salah/sudah kadaluarsa"
                ],400);
            }
    
            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
                $mailresp = Mail::to($user->email)->send(new Mailer($user));
                return response()->json([
                    "success"=>True,
                    "Message"=>"Email Berhasil Di Verifikasi"
                ],200);
            }
            else{
                return response()->json([
                    "success"=>True,
                    "Message"=>"Email sudah di verifikasi, tidak perlu verifikasi ulang"
                ],400);
            }
        }else{
            return response()->json([
                "success"=>False,
                "Message"=>"Link Verifikasi anda salah/sudah kadaluarsa"
            ],400);
        }
    }

    /**
     * Resend email verification link
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resend() {
        if (auth()->user()->hasVerifiedEmail()) {
                
        }

        auth()->user()->sendEmailVerificationNotification();

        return $this->respondWithMessage("Email verification link sent on your email id");
    }
}
