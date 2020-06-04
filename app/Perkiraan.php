<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Perkiraan extends Model
{
    protected $fillable = [
        'nama_perkiraan', 'rekening_id',
    ];
}
