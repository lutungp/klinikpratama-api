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

    public function getPasien(Request $request)
    {
        $input = $request->only("search_pasien");
        $search_pasien = isset($input['search_pasien']) ? $input['search_pasien'] : "";
        try {
            $select = ["pasien_id", "pasien_norm", "pasien_nama"];
            $qpasien = DB::table("m_pasien")->select($select);

            if ($search_pasien <> "") {
                $qpasien = $qpasien->whereRaw("UPPER(pasien_nama) LIKE '".strtoupper($search_pasien)."%'");
            }

            
            $respasien = $qpasien->where("m_pasien.pasien_aktif", "Y")
                                        ->orderBy('m_pasien.pasien_nama', 'ASC')
                                        ->limit(20)
                                        ->get();
                                        
            $respasienId = array_column($respasien->toArray(), "pasien_id");
            $respasienId = array_unique($respasienId);
            $respasienQuotes = "'" . implode("', '", $respasienId) . "'";

            $qdaftarexist = "   SELECT daftar_id, daftar_no, m_pasien_id, daftar_tanggal, m_pegawai.pegawai_nama
                                FROM
                                    t_pendaftaran
                                JOIN m_pegawai ON m_pegawai.pegawai_id = t_pendaftaran.m_pegawai_id
                                WHERE
                                    t_pendaftaran.daftar_aktif = 'Y' 
                                    AND t_pendaftaran.daftar_status != 'pulang' 
                                    AND t_pendaftaran.m_pasien_id IN ($respasienQuotes)";

            $resdaftarexist = DB::select($qdaftarexist);
            
            $datapasien = [];
            foreach ($respasien as $key => $value) {
                $pasien_id = $value->pasien_id;
                $rdaftarexist = array_filter($resdaftarexist, function ($val) use ($pasien_id)
                {
                    return $val->m_pasien_id == $pasien_id;
                });
                $daftar = count($rdaftarexist) > 0 ? $rdaftarexist[0] : [];
                $datapasien[] = [
                    "pasien_id" => $pasien_id,
                    "pasien_norm" => $value->pasien_norm,
                    "pasien_nama" => $value->pasien_nama,
                    "daftar_no" => empty($daftar) ? "" : "Sudah terdaftar " . date("d-m-Y", strtotime($daftar->daftar_tanggal)) . ", " . $daftar->daftar_no,
                    "daftar_tanggal" => empty($daftar) ? "" : $daftar->daftar_tanggal,
                    "pegawai_nama" => empty($daftar) ? "" : $daftar->pegawai_nama,
                ];
            }

            $result["status"] = "success";
            $result["datapasien"] = $datapasien;

            return response()->json($result, 200);
        } catch (\Throwable $th) {
            $result["status"] = "failure";
            return response()->json($result, 500);
        }
    }
}
