<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mapping extends Model
{
    use SoftDeletes;

    public function jenisTransaksi(){
        return $this->belongsTo('App\JenisTransaksi','transaksi_id','id');
    }

    public function rekening(){
        return $this->belongsTo('App\Rekening');
    }
}
