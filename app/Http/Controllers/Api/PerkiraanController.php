<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Perkiraan;

class PerkiraanController extends Controller
{
    public function get(){
        $all = Perkiraan::all();

        return $all;
    }

    public function addPerkiraan(Request $request){
        $this->validate($request,[
            'nama_perkiraan'=>'required|min:3',
            'rekening_id'=>'required|min:1'
        ]);
        
        $add = Perkiraan::create([
            'nama_perkiraan' => $request->nama_perkiraan,
            'rekening_id' => $request->rekening_id
        ]);
        return response()->json([
            'success' => true,
            'data' => $add
        ],200);
    }

    public function hapusPerkiraan(Request $request){
        $this->validate($request,[
            'id' => 'required'
        ]);
        $hapus = Perkiraan::find($request->id);
        $hapus->delete();

        return $hapus;
    }
}