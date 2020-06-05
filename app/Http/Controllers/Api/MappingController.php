<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mapping;
use App\Rekening;
use Illuminate\Http\Request;
use App\Http\Resources\Rekening as RekeningResource;
use App\Http\Resources\RekeningCollection;

class MappingController extends Controller
{

    public function get(Request $request)
    {
        /**Get Rekening Debit */
        $rekeningdebit = [];
        $mappingdebit = Mapping::where('transaksi_id', $request->transaksi_id)->where('tipe', 'D')->get();
        foreach ($mappingdebit as $m) {
            $rekening = Rekening::find($m->rekening_id);
            $rekeningdebit[] = [
                "rekening" => $m->rekening->nama_rekening,
                "data" => new RekeningCollection($rekening->perkiraan),
            ];
        }

        /**Get Rekening Kredit */
        $rekeningkredit = [];
        $mappingkredit = Mapping::where('transaksi_id', $request->transaksi_id)->where('tipe', 'K')->get();
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
        ]);
    }
}
