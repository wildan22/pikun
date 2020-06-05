<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jurnal;

class JurnalController extends Controller
{
    public function addJurnal(Request $request){
        $this->validate($request,[
            'tanggal'=>'required|date',
            'transaksi_id'=>'required|min:1',
            'perkiraan1_id'=>'required|min:1',
            'perkiraan2_id'=>'required|min:1',
            'keterangan'=>'nullable|min:3',
            'jumlah'=>'required|min:1|integer'
        ]);

        $add = Jurnal::create([
            'tanggal'=>$request->tanggal,
            'transaksi_id'=>$request->transaksi_id,
            'perkiraan1_id'=>$request->perkiraan1_id,
            'perkiraan2_id'=>$request->perkiraan2_id,
            'user_id' => auth()->user()->id,
            'keterangan'=>$request->keterangan,
            'jumlah'=>$request->jumlah
        ]);

        return response()->json([
            "success"=>True,
            "data"=>$add
        ],201);
    }
}
