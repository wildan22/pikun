<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Perusahaan;
use Auth;

class PerusahaanController extends Controller
{
    public function addPerusahaan(Request $request){
        $this->validate($request,[
            'nama_perusahaan'=>'required|min:3',
            'alamat' => 'required|min:3',
            'telepon' => 'required|number|min:10',
            'email' => 'required|email|min:3',
            'user_id' => 'required|min:1'
        ]);

        $add = Perusahaan::create([
            'nama_perusahaan' => $request->nama_perusahaan,
            'alamat' => $request->alamat,
            'telepon' => $request->telepon,
            'email' => $request->email,
            'user_id' => auth()->user()->id
        ]);

        return $add->additional([
            'success' => True
        ]);
    }
}
