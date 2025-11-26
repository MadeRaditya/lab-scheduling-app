<?php

namespace App\Models;

use App\Enums\JurusanAsisten;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asisten extends Authenticatable
{
    use HasFactory;
    protected $table = 'asistens';
    protected $primaryKey = 'id_asisten';
    public $timestamps = true;
    protected $fillable =
    ['nama', 'npm', 'jurusan', 'email', 'password', 'no_telepon', 'status_aktif'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = [
        'status_aktif' => 'boolean', 
        'tanggal_daftar' =>'datetime',
        'jurusan' => JurusanAsisten::class];
    public function kompetensis(): BelongsToMany
    {
        return $this->belongsToMany(
            Kompetensi::class,
            'asisten_kompetensi',
            'id_asisten',
            'id_kompetensi'
        )
            ->withPivot(['tingkat_penguasaan', 'tanggal_sertifikasi', 'sertifikasi'])
            ->withTimestamps();
    }
    public function asistenSemesters(): HasMany
    {
        return $this->hasMany(AsistenSemester::class, 'id_asisten');
    }
    public function jadwalAsistens(): HasMany
    {
        return $this->hasMany(JadwalAsisten::class, 'id_asisten');
    }
    public function kendalaAsistens(): HasMany
    {
        return $this->hasMany(KendalaAsisten::class, 'id_asisten');
    }
    public function penugasanAsistenFinals(): HasMany
    {
        return $this->hasMany(PenugasanAsistenFinal::class, 'id_asisten');
    }

    public function preferensiJadwalAsistens(): HasMany
    {
        return $this->hasMany(PreferensiJadwalAsisten::class, 'id_asisten');
    }
}
