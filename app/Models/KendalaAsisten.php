<?php

// app/Models/KendalaAsisten.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KendalaAsisten extends Model
{
    use HasFactory;

    protected $table = 'kendala_asisten';
    protected $primaryKey = 'id_kendala';
    public $timestamps = true;

    protected $fillable = ['id_asisten', 'id_semester', 'tipe_kendala', 'hari', 'tanggal_mulai', 'tanggal_selesai', 'alasan', 'status'];

    public function asisten(): BelongsTo
    {
        return $this->belongsTo(Asisten::class, 'id_asisten');
    }
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'id_semester');
    }
}
