<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\JenisTransaksi;
use App\Http\Resources\JenisTransaksi as JenisTransaksiResource;
use App\Http\Resources\JenisTransaksiCollection;


class JenisTransaksiController extends Controller
{
    public function getAllJenisTransaksi(){
        $all = JenisTransaksi::all();
        if($all){
            $res['success'] = true;
            return (new JenisTransaksiCollection($all))->additional($res);
        }
        else{
            $res['success'] = false;
            return $res;
        }


    }
}
