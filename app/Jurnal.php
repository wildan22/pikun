<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jurnal extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'tanggal','transaksi_id', 'perkiraan1_id','perkiraan2_id','user_id','keterangan','jumlah'
    ];

    public function jenisTransaksi(){
        return $this->belongsTo('App\JenisTransaksi','transaksi_id','id');
    }
    
    public function perkiraan1(){
        return $this->belongsTo('App\Perkiraan','perkiraan1_id','id');
    }

    public function perkiraan2(){
        return $this->belongsTo('App\Perkiraan','perkiraan2_id','id');
    }

}
