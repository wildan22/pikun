<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Str;
use Carbon\Carbon;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Http;
use App\User;
use App\Mail\Mailer;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    public function action(Request $request){
        $this->validate($request,[
            'email'=>'required|min:3|email',
            'password'=>'required|min:8'
        ]);

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $currentUser = auth()->user();
            ##VALIDATE IF USER ALREADY VERIFIED THE EMAIL
            if($currentUser->email_verified_at != Null){
                return (new UserResource($currentUser))->additional([
                    'meta'=>[
                        'token' => $currentUser->api_token
                    ],
                    'success' => true
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'error' => 'Mohon Verifikasi Email Anda Terlebih Dahulu'
                ],401);
            }
        }
        return response()->json([
            'success' => false,
            'error' => 'Username/password tidak Cocok'
        ],401);
    }


    public function loginWithGoogleToken(Request $request){
        $this->validate($request,[
            'gtoken'=>'required|min:10',
        ]);
        
        $response = Http::get('https://www.googleapis.com/oauth2/v1/tokeninfo', [
            'access_token' => $request->gtoken,
        ]);
        
        if($response->successful()){
            ///VALIDATE APP ISSUER
            if($response['issued_to'] == env('GOOGLE_CLIENT_ID')){
                $user = User::where('email', '=', $response['email'])->first();
                /**KONDISI KETIKA USER BELUM TERDAFTAR */
                
                if($user === null){
                    $googleuserinfo = Http::get('https://www.googleapis.com/oauth2/v3/userinfo',[
                        'access_token' => $request->gtoken,
                        'alt'=>'json'
                    ]);
                    $tmppassword = Str::random(10);
                    $data = [
                        'nama'=>$googleuserinfo['name'],
                        'password'=>$tmppassword,
                        'email'=>$response['email'],
                        'isusinggoogle'=>True
                    ];
                    $sakuuser = User::create([
                        'name' => $googleuserinfo['name'],
                        'email' => $response['email'],
                        'password' => bcrypt($tmppassword),
                        'api_token' => Str::random(80),
                        'email_verified_at' => Carbon::now()
                    ]);
                    $mailresp = Mail::to($response['email'])->send(new Mailer($data));
                    print($mailresp);
                    return (new UserResource($sakuuser))->additional([
                        'meta'=>[
                            'token' => $sakuuser->api_token
                        ],
                        'success' => true
                    ]);
                }
                /**KONDISI KETIKA USER SUDAH TERDAFTAR */
                else{
                    //VALIDATE IF USER EMAIL IS VALIDATED
                    if($user->email_verified_at != Null){
                        return (new UserResource($user))->additional([
                            'meta'=>[
                                'token' => $user->api_token
                            ],
                            'success' => true
                        ]);
                    }else{
                        return response()->json([
                            'success' => false,
                            'error' => 'Mohon Verifikasi Email Anda Terlebih Dahulu'
                        ],401);
                    }
                    
                }
            }else{
                return response()->json([
                    'success' => false,
                    'message' => 'App Issuer token tidak valid'
                ],401);
            }
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Token Expired, Silahkan Coba lagi'
            ],401);
        }

    }
}
