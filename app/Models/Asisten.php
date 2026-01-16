<?php

namespace App\Models;

use App\Enums\JurusanAsisten;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class Asisten extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $table = "asistens";
    protected $primaryKey = "id_asisten";
    public $timestamps = true;
    protected $fillable = [
        "nama",
        "npm",
        "jurusan",
        "email",
        "password",
        "no_telepon",
        "status_aktif",
    ];
    protected $hidden = ["password", "remember_token"];
    protected $casts = [
        "status_aktif" => "boolean",
        "tanggal_daftar" => "datetime",
        "jurusan" => JurusanAsisten::class,
    ];
    public function kompetensis(): BelongsToMany
    {
        return $this->belongsToMany(
            Kompetensi::class,
            "asisten_kompetensi",
            "id_asisten",
            "id_kompetensi",
        )
            ->withPivot([
                "tingkat_penguasaan",
                "tanggal_sertifikasi",
                "sertifikasi",
            ])
            ->withTimestamps();
    }
    public function asistenSemesters(): HasMany
    {
        return $this->hasMany(AsistenSemester::class, "id_asisten");
    }
    public function jadwalAsistens(): HasMany
    {
        return $this->hasMany(JadwalAsisten::class, "id_asisten");
    }
    public function kendalaAsistens(): HasMany
    {
        return $this->hasMany(KendalaAsisten::class, "id_asisten");
    }
    public function penugasanAsistenFinals(): HasMany
    {
        return $this->hasMany(PenugasanAsistenFinal::class, "id_asisten");
    }

    public function preferensiJadwalAsistens(): HasMany
    {
        return $this->hasMany(PreferensiJadwalAsisten::class, "id_asisten");
    }

    public function matakuliahs(): BelongsToMany
    {
        return $this->belongsToMany(
            Matakuliah::class,
            "asisten_matakuliah",
            "id_asisten",
            "matakuliah_id",
        )->withTimestamps();
    }

    public function getNameAttribute()
    {
        return $this->nama;
    }
    public function getAuthPassword()
    {
        return $this->password;
    }

    public function initials(): string
    {
        return Str::of($this->nama)
            ->explode(" ")
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode("");
    }
}
