<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalonAsisten extends Model
{
    use HasFactory;
    protected $fillable = ['nama', 'npm', 'email', 'token', 'is_registered'];

    protected $casts = [
        'is_registered' => 'boolean',
    ];
}
