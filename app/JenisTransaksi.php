<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mapping;

class JenisTransaksi extends Model
{
    public function mapping(){
        return $this->hasMany('App\Mapping');
    }
}
