<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Mapping;
use App\Perkiraan;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rekening extends Model
{
    use SoftDeletes;

    public function Mapping(){
        return $this->hasMany('App\Mapping');
    }

    public function perkiraan(){
        return $this->hasMany('App\Perkiraan');
    }
}
