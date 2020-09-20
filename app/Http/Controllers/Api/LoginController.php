<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Http\Resources\UserResource;

class LoginController extends Controller
{
    public function action(Request $request){
        $this->validate($request,[
            'email'=>'required|min:3|email',
            'password'=>'required|min:8'
        ]);

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $currentUser = auth()->user();

            return (new UserResource($currentUser))->additional([
                'meta'=>[
                    'token' => $currentUser->api_token
                ],
                'success' => true
            ]);
        }
        return response()->json([
            'success' => false,
            'error' => 'Username/password tidak Cocok'
        ],401);
    }
}
