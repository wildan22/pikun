<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mapping extends Model
{
    public function jenisTransaksi(){
        return $this->belongsTo('App\JenisTransaksi','transaksi_id','id');
    }

    public function rekening(){
        return $this->belongsTo('App\Rekening');
    }
}
