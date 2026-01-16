<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class MatakuliahKompetensi extends Pivot
{
    protected $table = 'matakuliah_kompetensi';
    public $incrementing = true;
    protected $keyType = 'string';
    protected $fillable = ['matakuliah_id', 'id_kompetensi', 'tingkat_dibutuhkan',  'prioritas'];
}
