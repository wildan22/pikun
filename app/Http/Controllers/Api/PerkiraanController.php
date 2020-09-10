<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Perkiraan;
use App\Mapping;
use App\Rekening;

class PerkiraanController extends Controller
{
    public function get(){
        $all = Perkiraan::all();

        return $all;
    }

    public function getPerkiraanBasedRekening($rekid){
        $perkiraan = Perkiraan::where('rekening_id',$rekid)->get();

        if($perkiraan){
            $res['success'] = true;
            $res['data'] = $perkiraan;
            return $res;
        }
        $res['success'] = false;
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

    public function getPerkiraanList(){
        $perkiraanlist = [];
        $map = Rekening::all();
        foreach ($map as $m) {
            $perkiraan = Perkiraan::where('rekening_id',$m->id)->get();
            $perkiraanlist[] = [
                "rekening" => $m->nama_rekening ."(".$m->tipe.")",
                "data" => $perkiraan,
            ];
        }
        return $perkiraanlist;
    }
}
