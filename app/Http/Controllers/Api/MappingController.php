<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mapping;
use App\Rekening;
use App\Perkiraan;
use Illuminate\Http\Request;
use App\Http\Resources\Rekening as RekeningResource;
use App\Http\Resources\RekeningCollection;

class MappingController extends Controller
{

    public function get($transaksi_id)
    {
        if($transaksi_id != null){
            /**Get Rekening Debit */
            $rekeningdebit = [];
            $mappingdebit = Mapping::where('transaksi_id', $transaksi_id)->where('tipe', 'D')->get();
            foreach ($mappingdebit as $m) {
                $rekening = Rekening::find($m->rekening_id);
                $rekeningdebit[] = [
                    "rekening" => $m->rekening->nama_rekening,
                    "data" => new RekeningCollection($rekening->perkiraan),
                ];
            }

            /**Get Rekening Kredit */
            $rekeningkredit = [];
            $mappingkredit = Mapping::where('transaksi_id', $transaksi_id)->where('tipe', 'K')->get();
            foreach ($mappingkredit as $m) {
                $rekening = Rekening::find($m->rekening_id);
                $rekeningkredit[] = [
                    "rekening" => $m->rekening->nama_rekening,
                    "data" => new RekeningCollection($rekening->perkiraan),
                ];
            }

            return response()->json([
                "data" => [
                    [
                        "jenis" => "D",
                        "data" => $rekeningdebit,
                    ],
                    [
                        "jenis" => "K",
                        "data" => $rekeningkredit,
                    ],
                ],
                "success"=>true,
            ]);
        }
        else{
            $res["success"] = false;
            $res["data"] = null;

            return $res;
        }

    }

    public function getRekeningKredit($transaksi_id){
        $rekeningkredit = [];
        $mappingkredit = Mapping::where('transaksi_id', $transaksi_id)->where('tipe', 'K')->get();
        foreach ($mappingkredit as $m) {
            $rekening = Rekening::find($m->rekening_id);
            $rekeningkredit[] = [
                "rekening" => $m->rekening->nama_rekening,
                "data" => new RekeningCollection($rekening->perkiraan),
            ];
        }


        return response()->json([
            "data" => $rekeningkredit,
            "success"=>true,
        ]);

    }

    public function getRekeningDebit($transaksi_id){
        $rekeningdebit = [];
        $mappingdebit = Mapping::where('transaksi_id', $transaksi_id)->where('tipe', 'D')->get();
        foreach ($mappingdebit as $m) {
            $rekening = Rekening::find($m->rekening_id);
            $rekeningdebit[] = [
                "rekening" => $m->rekening->nama_rekening,
                "data" => new RekeningCollection($rekening->perkiraan),
            ];
        }


        return response()->json([
            "data" => $rekeningdebit,
            "success"=>true,
        ]);

    }


}
