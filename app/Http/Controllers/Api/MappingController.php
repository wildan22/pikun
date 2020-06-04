<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mapping;
use App\Rekening;

class MappingController extends Controller
{

    public function get(Request $request){
        #$mapping = Mapping::where('transaksi_id',$request->transaksi_id)->get();
        $mappingdebit = Mapping::where('transaksi_id',$request->transaksi_id)->where('tipe','D')->get();
        //$mapping = Mapping::find(1);
        //return $all;

        //return $mapping;
        echo "Tipe : D\n";
        foreach($mappingdebit as $m){
            $rekening = Rekening::find($m->rekening_id);
            echo "Rekening : ".$m->rekening->nama_rekening;
            echo $rekening->perkiraan;
        }


        $mappingkredit = Mapping::where('transaksi_id',$request->transaksi_id)->where('tipe','K')->get();
        //$mapping = Mapping::find(1);
        //return $all;

        //return $mapping;
        echo "Tipe : K\n";
        foreach($mappingkredit as $m){
            $rekening = Rekening::find($m->rekening_id);
            echo "Rekening : ".$m->rekening->nama_rekening;
            echo $rekening->perkiraan;
        }
        //return $mapping->rekening->perkiraan;
    }
}
