<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jurnal;
use App\JurnalDetail;
use Auth;
use PDF;
use DB;

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
        if($add){
            #Menambahkan Perkiraan Pertama
            $tambahkredit = JurnalDetail::create([
                'perkiraan' => $request->perkiraan1_id,
                'jumlah'=>$request->jumlah,
                'jurnal_id'=> $add->id,
                'tipe' => "K"
            ]);
            $tambahkredit = JurnalDetail::create([
                'perkiraan' => $request->perkiraan2_id,
                'jumlah'=>$request->jumlah,
                'jurnal_id'=> $add->id,
                'tipe' => "D"
            ]);
        }
        return response()->json([
            "success"=>True,
            "data"=>$add
        ],201);
    }


    public function showJurnalList(Request $request){
        $this->validate($request,[
            'month'=>'required|min:1|integer',
            'year'=>'required|min:4|integer'
        ]);

        // $jurnalList = DB::select('SELECT jurnals.id,jurnals.tanggal,jurnals.user_id,jurnals.keterangan,jurnals.jumlah,perkiraan1.nama_perkiraan as perkiraan1,perkiraan2.nama_perkiraan as perkiraan2
        //                         FROM jurnals
        //                         INNER JOIN perkiraans as perkiraan1 ON jurnals.perkiraan1_id = perkiraan1.id
        //                         INNER JOIN perkiraans as perkiraan2 ON jurnals.perkiraan2_id = perkiraan2.id
        //                         WHERE user_id=?
        //                         AND MONTH(tanggal)=?
        //                         AND YEAR(tanggal)=?',[auth()->user()->id,$request->month,$request->year]);

        $jurnalList = DB::select('SELECT jurnals.id,jurnals.tanggal,jurnals.user_id,jurnals.keterangan,jurnals.jumlah,jurnals.perkiraan1_id,jurnals.perkiraan2_id
                                FROM jurnals
                                WHERE user_id=?
                                AND MONTH(tanggal)=?
                                AND YEAR(tanggal)=?',[auth()->user()->id,$request->month,$request->year]);

        return response()->json([
            "success"=>True,
            "data"=>$jurnalList
        ],200);
    }

    public function showSpecificJurnal($id){
        #$specificJurnal =
    }

    public function showSpecificJurnalDetail($id){
        $specificJurnalDetail = JurnalDetail::all();

        #$result =

        return $specificJurnalDetail;
    }

}
