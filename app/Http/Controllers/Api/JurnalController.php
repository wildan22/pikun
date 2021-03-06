<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jurnal;
use App\JurnalDetail;
use App\Perkiraan;
use Auth;
use PDF;
use DB;
use DateTime;
use Carbon\Carbon;

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
            return response()->json([
                "success"=>True,
                "data"=>$add
            ],201);
        }else{
            $delete = Jurnal::find($add->id);
            $delete->delete();
            return response()->json([
                "success"=>False,
                "data"=>[]
            ],201);
        }
    }

    public function deleteJurnal(Request $request){
        $this->validate($request,[
            'id'=>'required|min:1'
        ]);

        $jurnal = Jurnal::where('id',$request->id)->where('user_id',auth()->user()->id)->first();
        if($jurnal != Null){
            $deleteresponse = Jurnal::find($request->id)->delete();
            if($deleteresponse == True){
                return response()->json([
                    "success"=>True,
                    "message"=>"Jurnal Berhasil Dihapus"
                ],200);
            }else{
                return response()->json([
                    "success"=>False,
                    "message"=>"Jurnal Gagal Dihapus"
                ],400);
            }
        }
        else{
            return response()->json([
                "success"=>False,
                "message"=>"Jurnal Tidak Ditemukan/Jurnal sudah dihapus"
            ],400);
        }


    }

    public function editJurnal(Request $request){
        $this->validate($request,[
            'tanggal'=>'required|date',
            'id'=>'required|min:1',
            'jenis_transaksi' => 'required|min:1',
            'pilihan1' =>'required|min:1',
            'pilihan2' => 'required|min:1',
            'keterangan' => 'required|min:1',
            'nominal' => 'required|min:1'
        ]);
        $jurnal = Jurnal::where('id',$request->id)->where('user_id',auth()->user()->id);

        if($jurnal->first() != Null){
            $edit = $jurnal
            ->update([
                'transaksi_id'=>$request->jenis_transaksi,
                'perkiraan1_id'=>$request->pilihan1,
                'perkiraan2_id'=>$request->pilihan2,
                'keterangan'=>$request->keterangan,
                'jumlah'=>$request->nominal,
                'tanggal'=>$request->tanggal,
            ]);
            if($edit != 0){
                return response()->json([
                    "success"=>True,
                    "message"=>"Jurnal Berhasil Di Update"
                ],200);
            }else{
                return response()->json([
                    "success"=>False,
                    "message"=>"Jurnal Gagal Di Update"
                ],400);
            }
        }else{
            return response()->json([
                "success"=>False,
                "message"=>"Jurnal yang anda maksud tidak ada/sudah di hapus"
            ],400);
        }
    }


    public function showJurnalList(Request $request){
        $this->validate($request,[
            'month'=>'required|min:3',
            'year'=>'required|min:4|integer'
        ]);
        $data = [];

        $jurnalList = DB::select('SELECT jurnals.id,jurnals.transaksi_id,jurnals.tanggal,jurnals.user_id,jurnals.keterangan,jurnals.jumlah,jurnals.perkiraan1_id,jurnals.perkiraan2_id,perkiraan1.nama_perkiraan as perkiraan1,perkiraan2.nama_perkiraan as perkiraan2,jenistransaksi.jenis_transaksi as jt
                                FROM jurnals
                                INNER JOIN perkiraans as perkiraan1 ON jurnals.perkiraan1_id = perkiraan1.id
                                INNER JOIN perkiraans as perkiraan2 ON jurnals.perkiraan2_id = perkiraan2.id
                                INNER JOIN jenis_transaksis as jenistransaksi ON jurnals.transaksi_id = jenistransaksi.id
                                WHERE user_id=?
                                AND MONTHNAME(tanggal)=?
                                AND YEAR(tanggal)=?
                                AND jurnals.deleted_at IS NULL
                                ORDER BY tanggal',[auth()->user()->id,$request->month,$request->year]);

        foreach($jurnalList as $jl){
            $data[] = [
                "id_transaksi"=>$jl->id,
                "tanggal"=>$jl->tanggal,
                "keterangan"=>$jl->keterangan,
                "jumlah"=>$jl->jumlah,
                "perkiraan1"=>$jl->perkiraan1,
                "perkiraan1_id"=>$jl->perkiraan1_id,
                "perkiraan2"=>$jl->perkiraan2,
                "perkiraan2_id"=>$jl->perkiraan2_id,
                "jenis_transaksi"=>$jl->jt,
                "jenis_transaksi_id"=>$jl->transaksi_id
            ];

        }

        return response()->json($data,200);

        // return response()->json([
        //     "success"=>True,
        //     "data"=>$jurnalList
        // ],200);
    }



    public function showSpecificJurnalDetail($id){
        $specificJurnalDetail = JurnalDetail::where('jurnal_id',$id)->get();
        if($specificJurnalDetail != "[]"){
            $res['success'] = true;
            $res['message'] = "Data Berhasil Diambil";
            $res['tanggal'] = $specificJurnalDetail[0]->Jurnal['tanggal'];
            $res['nama_transaksi'] = $specificJurnalDetail[0]->Jurnal['keterangan'];
            $res['data'] = $specificJurnalDetail;
            return response($res,200);
        }
        else{
            $res['success'] = false;
            $res['message'] = "Data Gagal Diambil";
            return response($res,400);
        }
    }

    public function getRekeningId($perkiraan_id){
        if($perkiraan != "[]"){
            return $this->$perkiraan->rekening_id;
        }
        return null;
    }


    public function insertManualJurnal(Request $request){
        $this->validate($request,[
            'tanggal'=>'required',
            'nama_transaksi' => 'required',
            'detail_jurnal'=>'required',
            'keterangan' => 'required',
            'jumlah' => 'required'
        ]);
        $nominaldebit = 0;
        $nominalkredit = 0;

        //CREATE JURNAL
        $savejurnal = Jurnal::create([
            'tanggal'=>$request->tanggal,
            'user_id' => auth()->user()->id,
            'keterangan'=>$request->keterangan ,
            'jumlah'=>$request->jumlah
        ]);

        if($savejurnal != "[]"){
            foreach($request->detail_jurnal as $key => $value){
                if($request->detail_jurnal[$key]['tipe'] == "D"){
                    $nominaldebit = $nominaldebit + $request->detail_jurnal[$key]['jumlah'];
                }
                else if($request->detail_jurnal[$key]['tipe'] == "K"){
                    $nominalkredit = $nominalkredit + $request->detail_jurnal[$key]['jumlah'];
                }
                $detailJurnal[] =[
                    'perkiraan' => $request->detail_jurnal[$key]['kode_perkiraan'],
                    'jumlah' => $request->detail_jurnal[$key]['jumlah'],
                    'tipe' => $request->detail_jurnal[$key]['tipe'],
                    'jurnal_id' => $savejurnal->id,
                    'created_at'=>date('Y-m-d H:i:s'),
                    'updated_at'=> date('Y-m-d H:i:s')
                ];
            }

            //MENGECEK APAKAH JURNAL BALANCE
            if($nominaldebit == $nominalkredit){
                $insertRes = JurnalDetail::insert($detailJurnal);
                if($insertRes == true){
                    //Mengecek apakah jurnal berisi 2 data (untuk dibuat jurnal otomatisnya)
                    if(count($detailJurnal) == 2){
                        $update = Jurnal::find($savejurnal->id);

                        //Mencari Kode Rekening Perkiraan ke 1
                        $perkiraan = Perkiraan::firstWhere('id',$request->detail_jurnal[0]['kode_perkiraan']);
                        $perkiraan1_id = $perkiraan->rekening_id;
                        //Mencari Kode Rekening Perkiraan Ke 2
                        $perkiraan = Perkiraan::firstWhere('id',$request->detail_jurnal[1]['kode_perkiraan']);
                        $perkiraan2_id = $perkiraan->rekening_id;


                        #$update->perkiraan1_id = ;
                        #$update->perkiraan2_id = $request->detail_jurnal[1]['kode_perkiraan'];
                    }
                    $res['success'] = true;
                    $res['message'] = "Jurnal Berhasil Ditambahkan";
                    return $res;
                }
                else{
                    $res['success'] = false;
                    $res['message'] = "Jurnal Gagal Ditambahkan";
                    return $res;
                }
            }
            else{
                $res['success'] = false;
                $res['message'] = "Jurnal Tidak Balance";
                return $res;
            }


        }
        else{
            $res['success'] = false;
            $res['message'] = "Jurnal Gagal Ditambahkan";
            return $res;
        }
    }

    public function tampilkanDataTahun(Request $request){
        $year = DB::table('jurnals')
            ->select('tanggal')
            ->where('user_id',auth()->user()->id)
            ->where('deleted_at',NULL)
            ->groupBy(DB::raw('YEAR(tanggal) DESC'))
            ->get();

        if($year != "[]"){
            $res['success'] = true;
            $res['message'] = "Data Berhasil Diambil";
            
            //CREATING ARRAY TAHUN
            foreach($year as $y){
                $tmpyear = date('Y', strtotime($y->tanggal));
                #$res['tahun'] = $tmpyear;
                $tahun[] = $tmpyear;
            }
            $res['tahun'] = $tahun;
            
        }else{
            $res['success'] = true;
            $res['message'] = "Data Tahun Kosong, Menampilkan Tahun ini";
            $res['tahun'] = date("Y");
        }
        return response($res,200);
    }

}
