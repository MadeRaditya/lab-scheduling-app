<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JadwalFinal extends Model
{
    use HasFactory;

    protected $table = 'jadwal_finals';
    protected $primaryKey = 'id_final';
    public $timestamps = true;

    protected $fillable = ['id_semester', 'kode_mk', 'id_ruangan', 'hari', 'jam_mulai', 'jam_selesai', 'metode_penjadwalan', 'status', 'catatan'];

    protected $casts = [
        'jam_mulai' => 'string',
        'jam_selesai' => 'string',
        'tanggal_dibuat' => 'datetime'
    ];

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'id_semester');
    }
    public function matakuliah(): BelongsTo
    {
        return $this->belongsTo(Matakuliah::class, 'kode_mk');
    }
    public function ruangan(): BelongsTo
    {
        return $this->belongsTo(Ruangan::class, 'id_ruangan');
    }
    public function penugasanAsistenFinals(): HasMany
    {
        return $this->hasMany(PenugasanAsistenFinal::class, 'id_jadwal_final');
    }
}
