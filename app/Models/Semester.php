<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\SemesterJenis;
use App\Enums\SemesterStatus;


class Semester extends Model
{
    use HasFactory;
    protected $table = 'semesters';
    protected $primaryKey = 'id_semester';
    public $timestamps = true;
    protected $fillable =
    ['nama_semester', 'tahun_ajaran', 'jenis', 'status', 'tanggal_mulai', 'tanggal_selesai'];
    protected $casts = [
        'jenis' => SemesterJenis::class,
        'status' => SemesterStatus::class,
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];
    public function asistenSemesters(): HasMany
    {
        return $this->hasMany(AsistenSemester::class, 'id_semester');
    }
    public function jadwalLabs(): HasMany
    {
        return $this->hasMany(JadwalLab::class, 'id_semester');
    }
    public function jadwalFinals(): HasMany
    {
        return $this->hasMany(JadwalFinal::class, 'id_semester');
    }
}
