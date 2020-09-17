<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,

            //Data Perusahaan
            'nama_perusahaan' => $this->nama_perusahaan,
            'alamat_perusahaan' => $this->alamat_perusahaan,
            'telepon_perusahaan' => $this->telepon_perusahaan,
            'email_perusahaan' => $this->email_perusahaan,

            'joined' => $this->created_at->diffForHumans(),
            #'deleted' => $this->created_at->diffForHumans(),
        ];
    }
}
