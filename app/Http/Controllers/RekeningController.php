<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rekening;

class RekeningController extends Controller
{
    public function get(){
        $all = Rekening::all();

        return $all;
    }
}
