<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    protected $table = "m_pegawai";
    const CREATED_AT = 'pegawai_created_date';
    const UPDATED_AT = 'pegawai_updated_date';
}
