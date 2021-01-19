<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class AgamaController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getAgama()
    {
        $data = DB::table('m_agama')->select("agama_id", "agama_nama")
                    ->where("m_agama.agama_aktif", "y")
                    ->get();

        return response()->json($data, 200);
    }
}
