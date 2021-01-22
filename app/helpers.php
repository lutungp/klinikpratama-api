<?php

if (! function_exists('enableQueryLog')) {
    function enableQueryLog()
    {
        DB::enableQueryLog();
    }
}

if (! function_exists('getQueryLog')) {
    function getQueryLog()
    {
        dd(DB::getQueryLog());
    }
}

if (! function_exists('getPenomoran')) {
    function getPenomoran($jenis, $unit = 0, $tanggal, $table, $fieldid, $fieldno)
    {
        $unit_kode = "";
        $res = DB::table("s_penomoran")->where("penomoran_jenis", $jenis);
        if ($unit > 0) {
          $res = $res->where("s_penomoran.m_unit_id", $unit);
          // $unit_kode = DB::table("m_unit")->where("unit_id", $unit)->first();
          // if ($unit_kode) {
          //   $unit_kode = $unit_kode->unit_kode;
          // }
        }
        $res = $res->where("s_penomoran.penomoran_aktif", "Y");
        $res = $res->first();

        if ($res) {
          $awalan  = $res->penomoran_awal;
          $digit   = $res->penomoran_digitakhir;
          $separator = $res->penomoran_separator;
          $tanggal = $tanggal <> "" ? date($res->penomoran_tglformat, strtotime($tanggal)) : "";
        } else {
          $awalan = "";
          $digit = 8;
          $separator = "";
          $tanggal = "";
        }


        $nourut = DB::table($table)->select($fieldno)->orderby($fieldid, "DESC")->limit(1);
        if ($nourut->count() > 0) {
           $nourut = (array)$nourut->first();
           $nourut = $nourut[$fieldno];
           $nourut = (integer)preg_replace('/[^0-9]/', '', $nourut);
        } else {
           $nourut = 1;
        }
        $nourut = str_pad(($nourut + 1), $digit, "0", STR_PAD_LEFT );

        if ($unit_kode <> "") {
          $result = $awalan . $separator . $unit_kode . $separator . $tanggal . $separator . $nourut;
        } else {
          $result = $awalan . $separator . $tanggal . $separator . $nourut;
        }

        return $result;
    }
}

if (! function_exists('setBarangHna')) {
    function setBarangHna($data)
    {
        extract(get_object_vars($data));
        $datahna = array(
          'baranghna_tanggal' => isset($baranghna_tanggal) ? $baranghna_tanggal : null,
          'baranghna_jenis'   =>  isset($baranghna_jenis) ? $baranghna_jenis : null,
          'm_barang_id'       =>  isset($m_barang_id) ? $m_barang_id : null,
          'm_satuan_id'       =>  isset($m_satuan_id) ? $m_satuan_id : null,
          't_penerimaan_id'   =>  isset($t_penerimaan_id) ? $t_penerimaan_id : null,
          't_penerimaandet_id' =>  isset($t_penerimaandet_id) ? $t_penerimaandet_id : null,
          'baranghna_nilai_avg'   =>  isset($baranghna_nilai_avg) ? $baranghna_nilai_avg : null,
          'baranghna_nilai_new'   =>  isset($baranghna_nilai_new) ? $baranghna_nilai_new : null,
          'baranghna_nilai_max'   =>  isset($baranghna_nilai_max) ? $baranghna_nilai_max : null,
          'baranghna_status'  =>  isset($baranghna_status) ? $baranghna_status : null,
          'baranghna_created_by'  => isset($baranghna_created_by) ? $baranghna_created_by : null,
          'baranghna_created_date'=> isset($baranghna_created_date) ? $baranghna_created_date : null,
        );

        $result = DB::table("t_baranghna")->insert($datahna);
    }
}

if (! function_exists('setBarangHpp')) {
    function setBarangHpp($data)
    {
        extract(get_object_vars($data));
        $datahpp = array(
          'baranghpp_tanggal' => isset($baranghpp_tanggal) ? $baranghpp_tanggal : null,
          'baranghpp_jenis'   =>  isset($baranghpp_jenis) ? $baranghpp_jenis : null,
          'm_barang_id'       =>  isset($m_barang_id) ? $m_barang_id : null,
          'm_satuan_id'       =>  isset($m_satuan_id) ? $m_satuan_id : null,
          't_penerimaan_id'   =>  isset($t_penerimaan_id) ? $t_penerimaan_id : null,
          't_penerimaandet_id' =>  isset($t_penerimaandet_id) ? $t_penerimaandet_id : null,
          'baranghpp_nilai'   =>  isset($baranghpp_nilai) ? $baranghpp_nilai : null,
          'baranghpp_status'  =>  isset($baranghpp_status) ? $baranghpp_status : null,
          'baranghpp_created_by'  => isset($baranghpp_created_by) ? $baranghpp_created_by : null,
          'baranghpp_created_date'=> isset($baranghpp_created_date) ? $baranghpp_created_date : null,
        );
        $result = DB::table("t_baranghpp")->insert($datahpp);
    }
}

if (! function_exists('generateNoRM')) {
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


if (! function_exists('getRomawi')) {

  function getRomawi($bln){
    switch ($bln){
        case 1: 
            return "I";
            break;
        case 2:
            return "II";
            break;
        case 3:
            return "III";
            break;
        case 4:
            return "IV";
            break;
        case 5:
            return "V";
            break;
        case 6:
            return "VI";
            break;
        case 7:
            return "VII";
            break;
        case 8:
            return "VIII";
            break;
        case 9:
            return "IX";
            break;
        case 10:
            return "X";
            break;
        case 11:
            return "XI";
            break;
        case 12:
            return "XII";
            break;
    }
  }
}

if (! function_exists('getRevisionHuruf')) {

  function getRevisionHuruf($bln){
      $s = 'a';
      $s = chr(ord($s) + $bln);

      return $s;
  }

}

function penyebut($nilai) {
    $nilai = abs($nilai);
    $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    $temp = "";
    if ($nilai < 12) {
      $temp = " ". $huruf[$nilai];
    } else if ($nilai <20) {
      $temp = penyebut($nilai - 10). " belas";
    } else if ($nilai < 100) {
      $temp = penyebut($nilai/10)." puluh". penyebut($nilai % 10);
    } else if ($nilai < 200) {
      $temp = " seratus" . penyebut($nilai - 100);
    } else if ($nilai < 1000) {
      $temp = penyebut($nilai/100) . " ratus" . penyebut($nilai % 100);
    } else if ($nilai < 2000) {
      $temp = " seribu" . penyebut($nilai - 1000);
    } else if ($nilai < 1000000) {
      $temp = penyebut($nilai/1000) . " ribu" . penyebut($nilai % 1000);
    } else if ($nilai < 1000000000) {
      $temp = penyebut($nilai/1000000) . " juta" . penyebut($nilai % 1000000);
    } else if ($nilai < 1000000000000) {
      $temp = penyebut($nilai/1000000000) . " milyar" . penyebut(fmod($nilai,1000000000));
    } else if ($nilai < 1000000000000000) {
      $temp = penyebut($nilai/1000000000000) . " trilyun" . penyebut(fmod($nilai,1000000000000));
    }     
    return $temp;
}

if (! function_exists('terbilang')) {
  function terbilang($nilai) {
    if($nilai<0) {
      $hasil = "minus ". trim(penyebut($nilai));
    } else {
      $hasil = trim(penyebut($nilai));
    }         
    return $hasil;
  }
}

if (! function_exists('tgl_indo')) {
  function tgl_indo($tanggal){
    $bulan = array (
      1 =>   'Januari',
      'Februari',
      'Maret',
      'April',
      'Mei',
      'Juni',
      'Juli',
      'Agustus',
      'September',
      'Oktober',
      'November',
      'Desember'
    );
    $pecahkan = explode('-', $tanggal);
    
    // variabel pecahkan 0 = tanggal
    // variabel pecahkan 1 = bulan
    // variabel pecahkan 2 = tahun
   
    return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
  }
}

function getUmur($tanggal)
{
  //date in mm/dd/yyyy format; or it can be in other formats as well
  $birthDate = date("Y-m-d", $tanggal);
  //explode the date to get month, day and year
  $birthDate = explode("-", $birthDate);
  //get age from date or birthdate
  $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md")
    ? ((date("Y") - $birthDate[2]) - 1)
    : (date("Y") - $birthDate[2]));

  return $age;
}

if (! function_exists('hari_indo')) {
  function hari_indo($tanggal){
    $hari = array (
      1 =>   "SENIN",
      "SELASA",
      "RABU",
      "KAMIS",
      "JUM'AT",
      "SABTU",
      "MINGGU"
    );

    $angkahari = date('N', strtotime($tanggal));
    
    // variabel pecahkan 0 = tanggal
    // variabel pecahkan 1 = bulan
    // variabel pecahkan 2 = tahun
   
    return $hari[$angkahari];
  }
}

if (!function_exists('array_group_by')) {
  /**
   * Groups an array by a given key.
   *
   * Groups an array into arrays by a given key, or set of keys, shared between all array members.
   *
   * Based on {@author Jake Zatecky}'s {@link https://github.com/jakezatecky/array_group_by array_group_by()} function.
   * This variant allows $key to be closures.
   *
   * @param array $array   The array to have grouping performed on.
   * @param mixed $key,... The key to group or split by. Can be a _string_,
   *                       an _integer_, a _float_, or a _callable_.
   *
   *                       If the key is a callback, it must return
   *                       a valid key from the array.
   *
   *                       If the key is _NULL_, the iterated element is skipped.
   *
   *                       ```
   *                       string|int callback ( mixed $item )
   *                       ```
   *
   * @return array|null Returns a multidimensional array or `null` if `$key` is invalid.
   */
  function array_group_by(array $array, $key)
  {
    if (!is_string($key) && !is_int($key) && !is_float($key) && !is_callable($key) ) {
      trigger_error('array_group_by(): The key should be a string, an integer, or a callback', E_USER_ERROR);
      return null;
    }

    $func = (!is_string($key) && is_callable($key) ? $key : null);
    $_key = $key;

    // Load the new array, splitting by the target key
    $grouped = [];
    foreach ($array as $value) {
      $key = null;

      if (is_callable($func)) {
        $key = call_user_func($func, $value);
      } elseif (is_object($value) && property_exists($value, $_key)) {
        $key = $value->{$_key};
      } elseif (isset($value[$_key])) {
        $key = $value[$_key];
      }

      if ($key === null) {
        continue;
      }

      $grouped[$key][] = $value;
    }

    // Recursively build a nested grouping if more parameters are supplied
    // Each grouped array value is grouped according to the next sequential key
    if (func_num_args() > 2) {
      $args = func_get_args();

      foreach ($grouped as $key => $value) {
        $params = array_merge([ $value ], array_slice($args, 2, func_num_args()));
        $grouped[$key] = call_user_func_array('array_group_by', $params);
      }
    }

    return $grouped;
  }
}