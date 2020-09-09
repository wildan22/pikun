<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\JurnalDetail;
use DB;
use Auth;
use PDF;
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
class JurnalDetailController extends Controller
{
    public function generateJurnalPDF(Request $request){
        $this->validate($request,[
            'month'=>'required|min:1|integer',
            'year'=>'required|min:4|integer'
        ]);

        #$jurnalDetailList = JurnalDetail::all();

        $jurnalDetailList = DB::select('SELECT jurnals.tanggal,perkiraans.nama_perkiraan,jurnals.keterangan,jurnals.user_id,jurnal_details.tipe,jurnal_details.jumlah
                                        FROM jurnal_details
                                        INNER JOIN jurnals ON jurnal_details.jurnal_id=jurnals.id
                                        INNER JOIN perkiraans ON jurnal_details.perkiraan=perkiraans.id
                                        WHERE user_id=?
                                        AND MONTH(tanggal)=?
                                        AND YEAR(tanggal)=?',[auth()->user()->id,$request->month,$request->year]);

        $pdf = PDF::loadview('jurnal_report',['jurnal'=>$jurnalDetailList]);
    	return $pdf->download('Jurnal_report.pdf');
    }
}
