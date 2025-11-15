<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class AsistenSemester extends Model
{
    use HasFactory;

    protected $table = 'asisten_semesters';
    protected $primaryKey = 'id_asisten_semester';
    public $timestamps = true;

    protected $fillable = ['id_asisten', 'id_semester', 'status', 'peran', 'tanggal_mulai', 'tanggal_keluar', 'keterangan'];

    public function asisten(): BelongsTo
    {
        return $this->belongsTo(Asisten::class, 'id_asisten');
    }
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'id_semester');
    }
}
