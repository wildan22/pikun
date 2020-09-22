<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\JurnalDetail;
use DateTime;
use App\Perkiraan;
use DB;

class LaporanController extends Controller
{

    public function showJurnalReportAndroidJson(Request $request){
        $this->validate($request,[
            #'month'=>'required|min:1|str',
            'year'=>'required|min:4|integer'
        ]);

        $collection = DB::select('SELECT jurnals.id,jurnals.tanggal,jurnals.user_id,jurnals.keterangan,jurnals.jumlah,jurnals.perkiraan1_id,jurnals.perkiraan2_id,MONTH(jurnals.tanggal) as month
                    FROM jurnals
                    WHERE user_id=?
                    AND MONTHNAME(tanggal)=?
                    AND YEAR(tanggal)=?',[auth()->user()->id,$request->month,$request->year]);
        $res =[];
        $totaldebit = 0;
        $totalkredit = 0;
        if($collection){
            foreach($collection as $c){
                $jurnaldetail = [];
                $jurnalDetail= JurnalDetail::where('jurnal_id',$c->id)->with('Perkiraan')->get();
                foreach($jurnalDetail as $jd){
                    $jurnaldetail[] = [
                        'jumlah' => $jd->jumlah,
                        'jenis' => $jd->tipe,
                        'perkiraan' => $jd->Perkiraan->nama_perkiraan,
                    ];
                    if($jd->tipe == "D"){
                        $totaldebit += $jd->jumlah;
                    }
                    else if($jd->tipe == "K"){
                        $totalkredit += $jd->jumlah;
                    }
                }
                $monthNum = $c->month;
                $dateObj   = DateTime::createFromFormat('!m', $monthNum);
                $monthName = $dateObj->format('F');
                $res[] = [
                    'id' => $c->id,
                    'tanggal' => $c->tanggal,
                    'bulan' => $monthName,
                    'nama_transaksi' => $c->keterangan,
                    'jurnal_detail' => $jurnaldetail
                ];
                $message = "Data Berhasil Didapatkan";
            }

        }
        else{
            $message = "Data Jurnal Kosong";
        }
        return response()->json([
            "success"=>True,
            "data"=>collect($res)->groupBy('bulan')->all(),
            "totaldebit"=>$totaldebit,
            "totalkredit"=>$totalkredit,
            "message"=>$message
        ],200);

    }


    //Menampilkan Laporan Buku Besar untuk digunakan pada Android
    public function showBukuBesarJson(Request $request){
        $this->validate($request,[
            'month'=>'required|min:1|integer',
            'year'=>'required|min:4|integer',
            'perkiraan_id'=>'required|integer'
        ]);
        $jurnalDetail = JurnalDetail::where('perkiraan',$request->perkiraan_id)->get();
        $res=[];
        $total = 0;
        $totaldebit = 0;
        $totalkredit = 0;
        foreach($jurnalDetail as $jd){
            if($jd->tipe == "K"){
                $total -= $jd->jumlah;
                $totalkredit += $jd->jumlah;
            }
            else if($jd->tipe == "D"){
                $total += $jd->jumlah;
                $totaldebit += $jd->jumlah;
            }
            $d = date_parse_from_format("Y-m-d", $jd->Jurnal->tanggal);
            $monthNum =  $d["month"];
            $dateObj   = DateTime::createFromFormat('!m', $monthNum);
            $monthName = $dateObj->format('F');
            $res[] = [
                'id' => $jd->id,
                'jumlah' => $jd->jumlah,
                'jenis' => $jd->tipe,
                'keterangan' => $jd->Jurnal->keterangan,
                'bulan' => $monthName,
                'total' => $total
            ];
        }
        return response()->json([
            "success"=>True,
            "data"=>collect($res)->groupBy('bulan')->all(),
            "totaldebit"=>$totaldebit,
            "totalkredit"=>$totalkredit,
            "totalsaldo" => $totaldebit-$totalkredit
            #"message"=>$message
        ],200);
    }

    //Menampilkan Laporan Neraca Saldo untuk digunakan pada android
    public function showNeracaSaldo(Request $request){
        $this->validate($request,[
            'month'=>'required|min:1|integer',
            'year'=>'required|min:4|integer',
        ]);
        $jurnalDetail = JurnalDetail::select('perkiraan')->distinct()->get();
        $res =[];
        $temp_debit = 0;
        $temp_kredit = 0;
        foreach($jurnalDetail as $jd){
            $temp_total = 0;

            $jurnal_d = JurnalDetail::where('perkiraan',$jd->perkiraan)->get();
            foreach($jurnal_d as $j_d){
                if($j_d->tipe == "D"){
                    $temp_total += $j_d->jumlah;
                    $temp_debit +=$j_d->jumlah;
                }
                else if($j_d->tipe == "K"){
                    $temp_total -= $j_d->jumlah;
                    $temp_kredit -= $j_d->jumlah;
                }
                $d = date_parse_from_format("Y-m-d", $j_d->Jurnal->tanggal);
            }
            if($temp_total < 0){
                $tipe = "K";

            }
            else if($temp_total > 0){
                $tipe = "D";
            }
            $monthNum =  $d["month"];
            $dateObj   = DateTime::createFromFormat('!m', $monthNum);
            $monthName = $dateObj->format('F');
            $res[] =[
                'perkiraan' => $jd->Perkiraan->nama_perkiraan,
                'tipe'=>$tipe,
                'jumlah'=>abs($temp_total),
                'bulan'=>$monthName
            ];
        }

        return response()->json([
            "success"=>True,
            "data"=>collect($res)->groupBy('bulan')->all(),
            "totaldebit"=>$temp_debit,
            "totalkredit"=>$temp_kredit,
            #"message"=>$message
        ],200);
    }


    //Menampilkan Laporan Laba Rugi untuk digunakan pada android
    public function showLabaRugi(Request $request){
        $this->validate($request,[
            'month'=>'required|min:1|integer',
            'year'=>'required|min:4|integer',
        ]);

        //Pendapatan(7),Harga Pokok Penjualan(8),Biaya Penjualan(9),Biaya Admin dan Umum(10), Pendapatan Diluar Usaha(11), Biaya Diluar Usaha(12)
        $rekening_id = [7,8,9,10,11,12];
        $perkiraanid = [];
        foreach($rekening_id as $rek_id){
            $perkiraan = Perkiraan::where('rekening_id',$rek_id)->get();
            foreach($perkiraan as $p){
                $perkiraanid[] = [
                    'id'=>$p->id,
                    'nama'=>$p->nama_perkiraan
                ];
            }

        }



        //Mencari Laba Rugi
        //Pendapatan - HPP = Laba/Rugi Kotor
        $jurnalDetail = JurnalDetail::select('perkiraan')->distinct()->get();
        $perkiraanidnotnull = [];
        // foreach($jurnalDetail as $jd){
        //     $perkiraanidnotnull[] =[
        //         $jd->id
        //     ];
        // }


        return collect($perkiraanid)->all();
    }
}
