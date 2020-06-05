<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Perkiraan extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'nama_perkiraan', 'rekening_id',
    ];
}