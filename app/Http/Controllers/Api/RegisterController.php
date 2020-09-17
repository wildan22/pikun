<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Str;
use App\User;
use App\Http\Resources\UserResource;


class RegisterController extends Controller
{
    public function action(Request $request){
        $this->validate($request,[
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'nama_perusahaan' => 'required|min:3',
            'alamat_perusahaan' => 'required|min:3',
            'telepon_perusahaan' => 'required|min:10',
            'email_perusahaan' => 'required|min:3|email'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'api_token' => Str::random(80),

            //Perusahaan
            'nama_perusahaan' => $request->nama_perusahaan,
            'alamat_perusahaan' => $request->alamat_perusahaan,
            'telepon_perusahaan' => $request->telepon_perusahaan,
            'email_perusahaan' => $request->email_perusahaan
        ]);

        return (new UserResource($user))->additional([
            'meta' =>[
                'token' => $user->api_token
            ],
        ]);
    }
}

?>
