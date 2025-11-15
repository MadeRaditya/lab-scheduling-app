<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruangan extends Model
{
    use HasFactory;
    protected $table = 'ruangans';
    protected $primaryKey = 'id_ruangan';
    public $timestamps = true;
    protected $fillable =
    ['nama_ruangan', 'gedung', 'lantai', 'kapasitas', 'kategori', 'status'];
    public function jadwalLabs(): HasMany
    {
        return $this->hasMany(JadwalLab::class, 'id_ruangan');
    }
    public function jadwalFinals(): HasMany
    {
        return $this->hasMany(JadwalFinal::class, 'id_ruangan');
    }
}
