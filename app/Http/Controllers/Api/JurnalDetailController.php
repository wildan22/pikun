<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jurnal;
use App\JurnalDetail;
use DB;
use Auth;
use PDF;
use App\User;
use DateTime;
use App\Perkiraan;
use App\Rekening;

class JurnalDetailController extends Controller
{
    public function generateJurnalPDF(Request $request){
        $this->validate($request,[
            'month'=>'required|min:1|max:12',
            'year'=>'required|integer|min:1'
        ]);
        $monthname =  date('F', strtotime($request->month));
        $jurnalDetailList = DB::select('SELECT jurnals.tanggal,perkiraans.nama_perkiraan,jurnals.keterangan,jurnals.user_id,jurnal_details.tipe,jurnal_details.jumlah
                                        FROM jurnal_details
                                        INNER JOIN jurnals ON jurnal_details.jurnal_id=jurnals.id
                                        INNER JOIN perkiraans ON jurnal_details.perkiraan=perkiraans.id
                                        WHERE user_id=?
                                        AND MONTHNAME(tanggal)=?
                                        AND YEAR(tanggal)=?',[auth()->user()->id,$request->month,$request->year]);

        $perusahaanDetail = User::where('id',auth()->user()->id)->first();

        $pdf = PDF::loadview('jurnal_report',['jurnal'=>$jurnalDetailList,'perusahaan'=>$perusahaanDetail,'monthname'=>$monthname])->setPaper('a4', 'landscape');
    	return $pdf->download('Laporan Jurnal Umum.pdf');
    }


    //Tampilkan Neraca Saldo PDF
    public function generateNeracaSaldoPDF(Request $request){
        $this->validate($request,[
            'month'=>'required|min:3',
            'year'=>'required|min:4|integer',
        ]);

        $perusahaanDetail = User::where('id',auth()->user()->id)->first();


        //Convert Month Name to Number
        $monthnum =  date('m', strtotime($request->month));
        $yearnum = $request->year;

        $jurnalDetail = JurnalDetail::select('perkiraan')->whereHas('Jurnal',function($q) use($monthnum,$yearnum){
            $q->where('user_id',auth()->user()->id)->whereMonth('tanggal',$monthnum)->whereYear('tanggal',$yearnum);
        })->distinct()->get();
        $res =[];
        $temp_debit = 0;
        $temp_kredit = 0;
        foreach($jurnalDetail as $jd){
            $temp_total = 0;
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

        $json = json_encode([
                "success"=>True,
                "totaldebit"=>$temp_debit,
                "totalkredit"=>$temp_kredit,
                "data"=>collect($res)->groupBy('bulan')->all()

            ]);

        $pdf = PDF::loadview('neracasaldo_report',['response'=>$json,'perusahaan'=>$perusahaanDetail])->setPaper('a4', 'landscape');;
    	return $pdf->download('Laporan Neraca Saldo.pdf');
    }


    //Tampilkan Buku Besar PDF
    public function generateBukuBesarPDF(Request $request){
        $this->validate($request,[
            'month'=>'required|min:3',
            'year'=>'required|min:4|integer',
            'nama_perkiraan'=>'required'
        ]);

        //Getting Perkiraan Id based nama_perkiraan
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
        $json = json_encode([
            "success"=>True,
            "data"=>collect($res)->groupBy('bulan')->all(),
            "totaldebit"=>$totaldebit,
            "totalkredit"=>$totalkredit,
            "totalsaldo" => $totaldebit-$totalkredit
        ]);

        $pdf = PDF::loadview('bukubesar_report',['response'=>$json,'namaperkiraan'=>$request->nama_perkiraan])->setPaper('a4', 'landscape');;
        return $pdf->download('Laporan Buku Besar.pdf');
    }


    public function generateLabaRugiPDF(Request $request){
        $this->validate($request,[
            'month'=>'required|min:3',
            'year'=>'required|min:4|integer',
        ]);

        $perusahaanDetail = User::where('id',auth()->user()->id)->first();

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

        // return response()->json([
        //     "success"=>True,
        //     $request->month=>collect($json)->all(),
        //     "text"=>"Laba/Rugi Bersih",
        //     "total_keseluruhan"=>$totalkeseluruhan,
        // ],200);

        $json = json_encode([
            "success"=>True,
            "data"=> [$request->month=>collect($json)->all()],
            "text"=>"Laba/Rugi Bersih",
            "total_keseluruhan"=>$totalkeseluruhan,
        ]);

        
            
        $json = trim(preg_replace('/\s+/', ' ', $json));
        $pdf = PDF::loadview('labarugi_report',['response'=>$json,'perusahaan'=>$perusahaanDetail])->setPaper('a4', 'landscape');;
        return $pdf->download('Laporan Laba Rugi.pdf');
        
        #return $json;

    }

    public function generateNeracaPDF(Request $request){
        $perusahaanDetail = User::where('id',auth()->user()->id)->first();
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
                    "jumlah"=>($tmptotal<0 ? $tmptotal:$tmptotal),
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
                "total" => ($totalrekening<0 ? $totalrekening: $totalrekening),
                "perkiraan"=>$data
            ];

        }

        if($totalkeseluruhan < 0){
            $totalkeseluruhan = $totalkeseluruhan;
        }

        $json = json_encode([
            "success"=>True,
            "data"=>[$request->month=>collect($json)->all()],
            "text_a"=>"Total Aktiva",
            "total_aktiva"=>$totalaktiva,
            "text_b"=>"Total Utang dan Modal",
            "total_utang_modal"=>$totalutangmodal,
        ]);

        $json = trim(preg_replace('/\s+/', ' ', $json));
        $pdf = PDF::loadview('neraca_report',['response'=>$json,'perusahaan'=>$perusahaanDetail])->setPaper('a4', 'landscape');;
        return $pdf->download('Laporan Neraca.pdf');
    }

}
