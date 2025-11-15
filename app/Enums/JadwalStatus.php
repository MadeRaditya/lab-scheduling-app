<?php
namespace App\Enums;
enum JadwalStatus: string
{
case Draft = 'Draft';
case ButuhAsisten = 'Butuh Asisten';
case Terjadwal = 'Terjadwal';
case Selesai = 'Selesai';
}