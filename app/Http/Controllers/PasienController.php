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
        $input = $request->only("name", "notelp", "jnskelamin", "agama", "alamat", "tgllahir");
        // $validator = \Validator::make($input, [
        //     'name' => 'required|min:3|max:100',
        //     'notelp' => 'required|min:3|max:20',
        //     'jnskelamin' => 'required',
        //     'tgllahir' => 'required|date_format:Y-m-d',
        // ], [
        //     "name.required" => "Nama Pasien harus diiisi",
        //     "name.min" => "Nama Pasien minimal 3 karakter",
        //     "name.max" => "Nama Pasien maximal 100 karakter",
        //     "tgllahir.required" => "Tgl Lahir harus diiisi",
        //     "tgllahir.date_format" => "format tanggal salah (tahun-bulan-hari)",
        // ]);
        
        // if ($validator->fails()) {
        //     return response()->json(array(
        //         'success' => false,
        //         'errors' => $validator->getMessageBag()->toArray()
        //     ), 400); // 400 being the HTTP code for an invalid request.
        // }

        return response()->json($request, 200);
    }
}
