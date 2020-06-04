<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Mapping;
use App\Perkiraan;

class Rekening extends Model
{
    public function Mapping(){
        return $this->hasMany('App\Mapping');
    }

    public function perkiraan(){
        return $this->hasMany('App\Perkiraan');
    }
}
