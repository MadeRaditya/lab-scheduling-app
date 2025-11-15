<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenugasanAsistenFinal extends Model
{
    use HasFactory;

    protected $table = 'penugasan_asisten_finals';
    protected $primaryKey = 'id_penugasan';
    public $timestamps = true;

    protected $fillable = ['id_jadwal_final', 'id_asisten', 'id_kompetensi', 'skor_kecocokan', 'peran', 'status_kehadiran', 'catatan'];

    public function jadwalFinal(): BelongsTo
    {
        return $this->belongsTo(JadwalFinal::class, 'id_jadwal_final');
    }
    public function asisten(): BelongsTo
    {
        return $this->belongsTo(Asisten::class, 'id_asisten');
    }
    public function kompetensi(): BelongsTo
    {
        return $this->belongsTo(Kompetensi::class, 'id_kompetensi');
    }
}
