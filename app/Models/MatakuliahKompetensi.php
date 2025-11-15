<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MatakuliahKompetensi extends Pivot
{
    protected $table = 'matakuliah_kompetensi';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['kode_mk', 'id_kompetensi', 'tingkat_dibutuhkan',  'prioritas'];
}
