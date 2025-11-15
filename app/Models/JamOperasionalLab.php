<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class JamOperasionalLab extends Model
{
    use HasFactory;
    protected $table = 'jam_operasional_labs';
    protected $primaryKey = 'id_jam_op';
    public $timestamps = true;
    protected $fillable = ['hari', 'jam_buka', 'jam_tutup', 'keterangan'];
    protected $casts = [
        'jam_buka' => 'string',
        'jam_tutup' => 'string',
    ];
}
