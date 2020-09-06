<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jurnal;
use App\JurnalDetail;
use Auth;
use PDF;

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
        $jurnalList = Jurnal::where('user_id',auth()->user()->id)
                    ->whereYear('tanggal','=',$request->year)
                    ->whereMonth('tanggal','=',$request->month)
                    ->get();
        return response()->json([
            "success"=>True,
            "data"=>$jurnalList
        ],200);
    }

    public function generateJurnalPDF(Request $request){
        $this->validate($request,[
            'month'=>'required|min:1|integer',
            'year'=>'required|min:4|integer'
        ]);
        $jurnalList = Jurnal::where('user_id',auth()->user()->id)
                    ->whereYear('tanggal','=',$request->year)
                    ->whereMonth('tanggal','=',$request->month)
                    ->get();

        view()->share('jurnal',$jurnalList);
        $pdf = PDF::loadView('jurnal_report', $data);

        // download PDF file with download method
        return $pdf->download('jurnal_report.pdf');

    }
}
