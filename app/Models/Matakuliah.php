<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Matakuliah extends Model
{
    use HasFactory;
    protected $table = 'matakuliahs';
    protected $primaryKey = 'kode_mk';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = true;
    protected $fillable =
    ['kode_mk', 'nama_mk', 'sks', 'kelas', 'dosen', 'semester_berjalan', 'deskripsi'];
    public function kompetensis(): BelongsToMany
    {
        return $this->belongsToMany(
            Kompetensi::class,
            'matakuliah_kompetensi',
            'kode_mk',
            'id_kompetensi'
        )
            ->using(MatakuliahKompetensi::class)
            ->withPivot(['tingkat_dibutuhkan', 'prioritas'])
            ->withTimestamps();
    }
    public function jadwalLabs(): HasMany
    {
        return $this->hasMany(JadwalLab::class, 'kode_mk');
    }
    public function jadwalFinals(): HasMany
    {
        return $this->hasMany(JadwalFinal::class, 'kode_mk');
    }
}
