<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rekening;

class RekeningController extends Controller
{
    public function get(){
        $all = Rekening::all();
        if($all){
            $res['success'] = true;
            $res['data'] = $all;
            return $res;
        }
        $res['success'] = false;
    }
}
