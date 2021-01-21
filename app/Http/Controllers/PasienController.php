<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class PasienController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function createPasien(Request $request)
    {
        $input = $request->only("name", "notelp", "gender", "agama", "alamat", "tgllahir");
        $validator = \Validator::make($input, [
            'name' => 'required|min:3|max:100',
            'notelp' => 'required|min:3|max:20',
            'gender' => 'required',
            'tgllahir' => 'required|date_format:Y-m-d',
        ], [
            "name.required" => "Nama Pasien harus diiisi",
            "name.min" => "Nama Pasien minimal 3 karakter",
            "name.max" => "Nama Pasien maximal 100 karakter",
            "tgllahir.required" => "Tgl Lahir harus diiisi",
            "tgllahir.date_format" => "format tanggal salah (tahun-bulan-hari)",
        ]);
        
        if ($validator->fails()) {
            return response()->json(array(
                'success' => false,
                'errors' => $validator->getMessageBag()->toArray()
            ), 400); // 400 being the HTTP code for an invalid request.
        }

        $data = array(
            "pasien_nama"     => $input["name"],
            "pasien_kelamin"  => $input["gender"],
            "pasien_tgllahir" => date("Y-m-d", strtotime($input["tgllahir"])),
            "pasien_nohp"     => $input["notelp"],
            "pasien_alamat"   => $input["alamat"],
            "m_agama_id"      => $input["agama"]
        );

        $data["pasien_norm"]  = $this->generateNoRM();
        $data["pasien_norm2"]  = intval($data["pasien_norm"]);
        $data['pasien_created_by']    = \Auth::user()->name;
        $data['pasien_created_date']  = date("Y-m-d H:i:s");
        // $exe = $result = DB::table("m_pasien")->insert($data);
        $exe = true;
        if ($exe) {
            $res["status"] = "success";
            $res["norm"] = $data["pasien_norm"];
            return response()->json($res, 200);
        } else {
            $res["status"] = "failure";
            return response()->json($res, 202);
        }

        
    }

    function generateNoRM()
    {
        $lastpasien = DB::table('m_pasien')->orderby('pasien_id', 'desc')->first();
        $nextrm = 1;
        if ($lastpasien) {
            $lastrm = (integer)$lastpasien->pasien_norm;
            $nextrm = $lastrm+1;
        }

        $newrm = str_pad(($nextrm), 8, "0", STR_PAD_LEFT );
        return $newrm;
    }
}
