<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\JurnalDetail;
use DateTime;
use App\Perkiraan;
use App\Rekening;
use DB;

class LaporanController extends Controller
{

    public function showJurnalReportAndroidJson(Request $request){
        $this->validate($request,[
            'month'=>'required|min:1',
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
            'month'=>'required|min:3',
            'year'=>'required|min:4|integer',
            'nama_perkiraan'=>'required'
            #'perkiraan_id'=>'required|integer'
        ]);


        //Getting Perkiraan Id based nama_perkiraan
        #$perkiraan = Perkiraan::where('nama_perkiraan','like','%'.$request->nama_perkiraan.'%')->first();
        $perkiraan = Perkiraan::where('nama_perkiraan',$request->nama_perkiraan)->first();
        if($perkiraan != Null){
            $perkiraan_id = $perkiraan->id;
        }
        else{
            $perkiraan_id = 0;
        }


        //Convert Month Name to Number
        $monthnum =  date('m', strtotime($request->month));
        $yearnum = $request->year;

        $jurnalDetail = JurnalDetail::where('perkiraan',$perkiraan_id)->whereHas('Jurnal',function($q) use($monthnum,$yearnum){
            $q->where('user_id',auth()->user()->id)->whereMonth('tanggal',$monthnum)->whereYear('tanggal',$yearnum);
        })->get();

        // $jurnalDetail = JurnalDetail::where('perkiraan',$request->perkiraan_id)->whereHas('Jurnal',function($q) use($monthnum,$yearnum){
        //     $q->where('user_id',auth()->user()->id)->whereMonth('tanggal',$monthnum)->whereYear('tanggal',$yearnum);
        // })->get();


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
                'tanggal' => $jd->Jurnal->tanggal,
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
            'month'=>'required|min:3',
            'year'=>'required|min:4|integer',
        ]);


        //Convert Month Name to Number
        $monthnum =  date('m', strtotime($request->month));
        $yearnum = $request->year;


        #$jurnalDetail = JurnalDetail::select('perkiraan')->distinct()->get();
        $jurnalDetail = JurnalDetail::select('perkiraan')->whereHas('Jurnal',function($q) use($monthnum,$yearnum){
            $q->where('user_id',auth()->user()->id)->whereMonth('tanggal',$monthnum)->whereYear('tanggal',$yearnum);
        })->distinct()->get();
        $res =[];
        $temp_debit = 0;
        $temp_kredit = 0;
        foreach($jurnalDetail as $jd){
            $temp_total = 0;

            #$jurnal_d = JurnalDetail::where('perkiraan',$jd->perkiraan)->get();
            $jurnal_d = JurnalDetail::where('perkiraan',$jd->perkiraan)->whereHas('Jurnal',function($q) use($monthnum,$yearnum){
                $q->where('user_id',auth()->user()->id)->whereMonth('tanggal',$monthnum)->whereYear('tanggal',$yearnum);
            })->distinct()->get();
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
            'month'=>'required|min:3',
            'year'=>'required|min:4|integer',
        ]);

        //Convert Month Name to Number
        $monthnum =  date('m', strtotime($request->month));
        $yearnum = $request->year;
        //Pendapatan(7),Harga Pokok Penjualan(8),Biaya Penjualan(9),Biaya Admin dan Umum(10), Pendapatan Diluar Usaha(11), Biaya Diluar Usaha(12)
        $rekening_id = [7,8,9,10,11,12];
        $text = ["Pendapatan Bersih","LABA/RUGI KOTOR","Total Biaya Penjualan","Total Admin dan Umum","Total Pendapatan Diluar Usaha","Total Biaya Diluar Usaha"];
        $totalkeseluruhan = 0;
        $perkiraanid = [];
        foreach($rekening_id as $key=>$rek_id){
            $totalrekening = 0;
            $data = [];
            $perkiraan = Perkiraan::where('rekening_id',$rek_id)->get();
            foreach($perkiraan as $p){
                $tmptotal = 0;
                $total = 0;
                $jurnaldetail = JurnalDetail::where('perkiraan',$p->id)->whereHas('Jurnal',function($q) use($monthnum,$yearnum){
                    $q->where('user_id',auth()->user()->id)->whereMonth('tanggal',$monthnum)->whereYear('tanggal',$yearnum);
                })->get();

                foreach($jurnaldetail as $jd){

                    if($jd->tipe == "K"){
                        $tmptotal += $jd->jumlah;
                    }
                    else if($jd->tipe == "D"){
                        $tmptotal -= $jd->jumlah;
                    }
                }
                if($tmptotal<0){
                    $total = "(".abs($tmptotal).")";
                }
                else if($tmptotal>0){
                    $total = $tmptotal;
                }
                $data[] = [
                    "nama_perkiraan"=>$p->nama_perkiraan,
                    "jumlah"=>$total,
                ];

                $totalrekening += $tmptotal;

                #$jurnaldetail->where('perkiraan',$p->id);
            }
            $totalkeseluruhan += $totalrekening;
            //Kode Pendapatan
            if($rek_id == "7"){
                $pendapatan = $totalrekening;
            }
            elseif($rek_id == "8"){
                $totalrekening = $pendapatan + $totalrekening;
            }

            if($totalrekening<0){
                $totalrekening = "(".abs($totalrekening).")";
            }

            $rek = Rekening::where('id',$rek_id)->first();
            $json[] = [
                "rekening"=> $rek->nama_rekening,
                "text" => $text[$key],
                "total" => $totalrekening,
                "perkiraan"=>$data
            ];

        }

        if($totalkeseluruhan < 0){
            $totalkeseluruhan = "(".abs($totalkeseluruhan).")";
        }

        return response()->json([
            "success"=>True,
            $request->month=>collect($json)->all(),
            "text"=>"Laba/Rugi Bersih",
            "total_keseluruhan"=>$totalkeseluruhan,
        ],200);
    }


    //Menampilkan Laporan Neraca
    public function showNeraca(Request $request){

        //Convert Month Name to Number
        $monthnum =  date('m', strtotime($request->month));
        $yearnum = $request->year;

        $rekening_id = [7,8,9,10,11,12];
        $totalrabalugi = 0;
        $perkiraanid = [];
        foreach($rekening_id as $key=>$rek_id){
            $totalrekening = 0;
            $data = [];
            $perkiraan = Perkiraan::where('rekening_id',$rek_id)->get();
            foreach($perkiraan as $p){
                $tmptotal = 0;
                $total = 0;
                $jurnaldetail = JurnalDetail::where('perkiraan',$p->id)->whereHas('Jurnal',function($q) use($monthnum,$yearnum){
                    $q->where('user_id',auth()->user()->id)->whereMonth('tanggal',$monthnum)->whereYear('tanggal',$yearnum);
                })->get();

                foreach($jurnaldetail as $jd){

                    if($jd->tipe == "K"){
                        $tmptotal += $jd->jumlah;
                    }
                    else if($jd->tipe == "D"){
                        $tmptotal -= $jd->jumlah;
                    }
                }
                if($tmptotal<0){
                    $total = "(".abs($tmptotal).")";
                }
                else if($tmptotal>0){
                    $total = $tmptotal;
                }
                $data[] = [
                    "nama_perkiraan"=>$p->nama_perkiraan,
                    "jumlah"=>$total,
                ];

                $totalrekening += $tmptotal;

                #$jurnaldetail->where('perkiraan',$p->id);
            }
            $totalrabalugi += $totalrekening;
            //Kode Pendapatan

            if($totalrekening<0){
                $totalrekening = "(".abs($totalrekening).")";
            }

        }

        if($totalrabalugi < 0){
            $totallabarugi = "(".abs($totalrabalugi).")";
        }





        $totalkeseluruhan = 0;
        $totalaktiva = 0;
        $totalutangmodal = 0;

        $perkiraanid = [];
        $text = ["Total Aktiva Lancar","Total Aktiva Tetap","Total Utang Jangka Pendek","Total Utang Jangka Panjang","Total Modal"];

        //Aktiva Lancar (1), Aktiva Tetap(2)
        $rekening_id = [1,2,4,5,6];
        foreach($rekening_id as $key=>$rek_id){
            $totalrekening = 0;
            $data = [];
            $perkiraan = Perkiraan::where('rekening_id',$rek_id)->get();
            foreach($perkiraan as $p){
                $tmptotal = 0;
                $total = 0;
                $jurnaldetail = JurnalDetail::where('perkiraan',$p->id)->whereHas('Jurnal',function($q) use($monthnum,$yearnum){
                    $q->where('user_id',auth()->user()->id)->whereMonth('tanggal',$monthnum)->whereYear('tanggal',$yearnum);
                })->get();

                foreach($jurnaldetail as $jd){
                    //Menghitung Total Tiap Jurnal
                    if($jd->tipe == "K"){
                        $tmptotal += $jd->jumlah;
                    }
                    else if($jd->tipe == "D"){
                        $tmptotal -= $jd->jumlah;
                    }
                }
                $data[] = [
                    "nama_perkiraan"=>$p->nama_perkiraan,
                    "jumlah"=>($tmptotal<0 ? "(".abs($tmptotal).")":$tmptotal),
                ];

                $totalrekening += $tmptotal;

            }

            if($rek_id == 6){
                $data[] =[
                    "nama_perkiraan"=>"Laba / Rugi Bersih",
                    "jumlah"=>abs($totalrabalugi)
                ];
                $totalrekening += abs($totalrabalugi);

            }

            $totalkeseluruhan += $totalrekening;
            if($rek_id == 1 || $rek_id == 2){
                $totalaktiva += $totalrekening;
            }
            else if($rek_id == 4 || $rek_id == 5 || $rek_id == 6){
                $totalutangmodal += $totalrekening;
            }


            $rek = Rekening::where('id',$rek_id)->first();
            $json[] = [
                "rekening"=> $rek->nama_rekening,
                "text" => $text[$key],
                "total" => ($totalrekening<0 ? "(".abs($totalrekening).")" : $totalrekening),
                "perkiraan"=>$data
            ];

        }

        if($totalkeseluruhan < 0){
            $totalkeseluruhan = "(".abs($totalkeseluruhan).")";
        }

        return response()->json([
            "success"=>True,
            $request->month=>collect($json)->all(),
            "text_a"=>"Total Aktiva",
            "total_aktiva"=>$totalaktiva,
            "text_b"=>"Total Utang dan Modal",
            "total_utang_modal"=>$totalutangmodal,
        ],200);
    }
}
