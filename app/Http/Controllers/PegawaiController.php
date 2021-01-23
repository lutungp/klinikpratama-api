<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pegawai;
use DB;

class PegawaiController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getPegawaiAdmisi(Request $request)
    {
        $input = $request->only("unit_id", "tgl", "jam", "daftar_jenis", "search_pegawai", "pageSize");
        
        $unit_id = isset($input["unit_id"]) ? $input["unit_id"] : 0;
        $tgl = isset($input["tgl"]) ? date("Y-m-d", strtotime($input["tgl"])) : "";
        $jam = isset($input["jam"]) ? $input["jam"] : "";
        $daftar_jenis = isset($input["daftar_jenis"]) ? $input["daftar_jenis"] : '';
        $resultAkses = false;
        $pegawai_idakses = [];
        if ($unit_id > 0) {
            $resultAkses = DB::table("m_pegawai_unit")->where("m_unit_id", $unit_id)->get();
            foreach ($resultAkses as $key => $value) {
                array_push($pegawai_idakses, $value->m_pegawai_id);
            }
        }

        $select = [ "pegawai_id", "pegawai_nama","pegawai_namapanggilan","pegawai_no", "m_fungsional_id"];

        $search_pegawai = $input['search_pegawai'];
        $pegawai = Pegawai::select($select);
        /* jadwal dokter hanya berlaku di POLI/RAWAT JALAN */
        if ($unit_id > 0 && $jam <> "" && $tgl <> "" && $daftar_jenis !== 'INST. RAWAT INAP') {
            /* jika mencari berdasarkan akses ke suatu unit */
            $pegawai = $pegawai->join("s_jadwal_dokterdet", function ($join)
                            {
                                $join->on("s_jadwal_dokterdet.m_pegawai_id", "m_pegawai.pegawai_id");
                            });
        }

        $pegawai = $pegawai->where("pegawai_aktif", "Y");

        if ($search_pegawai <> "") {
            $pegawai = $pegawai->where(function ($query) use ($search_pegawai){
              $query->whereRaw("UPPER(pegawai_nama) like '%" . $search_pegawai . "%'")
                    ->orWhereRaw("UPPER(pegawai_no) like '%" . $search_pegawai . "%'");
            });
        }

        if ($unit_id > 0 && $daftar_jenis !== 'INST. RAWAT INAP') { /* jika mencari berdasarkan akses ke suatu unit */
            $pegawai = $pegawai->whereIn("pegawai_id", $pegawai_idakses);
            if ($jam <> "" && $tgl <> "") {
                $hari = hari_indo($tgl);
                $pegawai = $pegawai->where("s_jadwal_dokterdet.m_unit_id", $unit_id)
                                ->where("s_jadwal_dokterdet.jadwaldokterdet_tutup", 'N')
                                ->whereRaw("TIME(s_jadwal_dokterdet.jadwaldokterdet_mulai) <= '". $jam ."'")
                                ->whereRaw("TIME(s_jadwal_dokterdet.jadwaldokterdet_selesai) >= '". $jam ."'")
                                ->where("jadwaldokterdet_hari", $hari);
            }
        }

        $pegawai = $pegawai->orderBy('pegawai_nama', 'ASC')
                        ->limit($input['pageSize'])
                        ->get();

        return response()->json($pegawai, 200);
    }
}