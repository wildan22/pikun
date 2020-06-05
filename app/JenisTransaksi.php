<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mapping;
use Illuminate\Database\Eloquent\SoftDeletes;


class JenisTransaksi extends Model
{
    use SoftDeletes;
    public function mapping(){
        return $this->hasMany('App\Mapping');
    }
}
