<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\JenisTransaksi;


class JenisTransaksiController extends Controller
{
    public function getAllJenisTransaksi(){
        $all = JenisTransaksi::all();

        return $all;

    }
}
