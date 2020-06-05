<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JenisTransaksi extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'=>$this->id,
            'jenis'=>$this->jenis_transaksi,
            'keterangan1'=>$this->keterangan1,
            'keterangan2'=>$this->keterangan2
        ];
    }
}
