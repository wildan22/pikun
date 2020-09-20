<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JurnalDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'perkiraan','jumlah', 'jurnal_id','tipe',
    ];

    public function Jurnal(){
        return $this->belongsTo('App\Jurnal','jurnal_id','id');
    }

    public function Perkiraan(){
        return $this->belongsTo('App\Perkiraan','perkiraan','id');
    }
}
