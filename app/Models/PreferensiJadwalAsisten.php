<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreferensiJadwalAsisten extends Model
{
    use HasFactory;

    protected $table = 'preferensi_jadwal_asisten';
    protected $primaryKey = 'id_preferensi';
    public $timestamps = true;

    protected $fillable = ['id_asisten','id_semester','hari','jam_mulai','jam_selesai','tingkat_preferensi','keterangan'];

    protected $casts = ['jam_mulai' => 'string','jam_selesai' => 'string'];

    public function asisten(): BelongsTo { return $this->belongsTo(Asisten::class, 'id_asisten'); }
    public function semester(): BelongsTo { return $this->belongsTo(Semester::class, 'id_semester'); }
}