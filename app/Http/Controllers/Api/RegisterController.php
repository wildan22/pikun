<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Str;
use App\User;


class RegisterController extends Controller
{
    public function action(Request $request){
        $this->validate($request,[
            'name' => 'required|min:3',
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);
        $user = User::where('email','=',$request->email)->first();
        //USER BELUM TERDAFTAR SAMA SEKALI
        if($user == null){
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'api_token' => Str::random(80),
            ]);
            $user->sendEmailVerificationNotification();
            if($user){
                return response()->json([
                    "success"=>True,
                    "message"=>"Mohon cek email anda untuk menkonfirmasi akun",
                ],200);
            }
        }
        //USER SUDAH TERDAFTAR DAN BELUM VERIFIKASI -> MENGIRIM ULANG KODE VEIRIFKASI
        elseif($user != null && $user->email_verified_at == Null){
            $user->sendEmailVerificationNotification();
            return response()->json([
                "success"=>True,
                "message"=>"Akun anda sudah pernah didaftarkan sebelumnya, mohon cek email anda untuk mem verifikasi akun",
            ],200);
        }else{
            return response()->json([
                "success"=>False,
                "message"=>"Akun anda sudah terdaftar, dan sudah diverifikasi",
            ],400);
        }
    }
}

?>
