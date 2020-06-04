<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Auth;
use App\User;
use App\Perusahaan;

class UserController extends Controller
{
    public function me(){
        $user = auth()->user();

        return new UserResource($user);
    }

    public function updateInfo(Request $request){
        $this->validate($request,[
            'name' => 'required|min:3'
        ]);

        $updateuser = User::find(auth()->user()->id);
        $updateuser->name = $request->name;
        $updateuser->save();

        return (new UserResource($updateuser))->additional([
            'success'=>True
        ]);
    }

    public function closeAccount(){
        $closeaccount = User::find(auth()->user()->id);
        $closeaccount->delete();

        return (new UserResource($closeaccount))->additional([
            'success'=>True
        ]);
    }

    public function addPerusahaan(Request $request){
        $this->validate($request,[
            'nama_perusahaan'=>'required|min:3',
            'alamat' => 'required|min:3',
            'telepon' => 'required|min:10',
            'email' => 'required|email|min:3',
        ]);

        $add = Perusahaan::create([
            'nama_perusahaan' => $request->nama_perusahaan,
            'alamat' => $request->alamat,
            'telepon' => $request->telepon,
            'email' => $request->email,
            'user_id' => auth()->user()->id
        ]);

        return $add;
    }

    public function editDataPerusahaan(Request $request){
        $this->validate($request,[
            'id' => 'required',
            'nama_perusahaan'=>'required|min:3',
            'alamat' => 'required|min:3',
            'telepon' => 'required|min:10',
            'email' => 'required|email|min:3',
            
        ]);
        
        $updateperusahaan = Perusahaan::find($request->id);
        $updateperusahaan->where('user_id',auth()->user()->id);
        $updateperusahaan->nama_perusahaan = $request->nama_perusahaan;
        $updateperusahaan->alamat = $request->alamat;
        $updateperusahaan->telepon = $request->telepon;
        $updateperusahaan->email = $request->email;

        $updateperusahaan->save();

        return $updateperusahaan;
    }

    public function hapusPerusahaan(Request $request){
        $this->validate($request,[
            'id'=>'required|min:1'
        ]);
        $deleteperusahaan = Perusahaan::find($request->id);
        $deleteperusahaan->where('user_id',auth()->user()->id);
        $deleteperusahaan->delete();

        return $deleteperusahaan;
    }

    public function getDaftarPerusahaan(){
        $perusahaan = Perusahaan::where('user_id',auth()->user()->id)->get();

        return $perusahaan;
    }
}
