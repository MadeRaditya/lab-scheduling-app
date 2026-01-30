<?php

namespace App\Enums;

enum PreferensiTingkat: string
{
    case PrioritasUtama = 'Prioritas Utama';
    case Bersedia = 'Bersedia';
    case Cadangan = 'Cadangan';
    case TidakBersedia = 'Tidak Bersedia';
}
