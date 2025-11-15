<?php

namespace App\Enums;
enum PenugasanStatusKehadiran: string
{
case Terjadwal = 'Terjadwal';
case Hadir = 'Hadir';
case TidakHadir = 'Tidak Hadir';
case Izin = 'Izin';
}