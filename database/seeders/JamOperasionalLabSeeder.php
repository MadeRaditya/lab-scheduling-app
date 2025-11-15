<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JamOperasionalLabSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['hari'=>'Senin','jam_buka'=>'07:30:00','jam_tutup'=>'16:30:00','keterangan'=>'Hari normal'],
            ['hari'=>'Selasa','jam_buka'=>'07:30:00','jam_tutup'=>'16:30:00','keterangan'=>'Hari normal'],
            ['hari'=>'Rabu','jam_buka'=>'07:30:00','jam_tutup'=>'16:30:00','keterangan'=>'Hari normal'],
            ['hari'=>'Kamis','jam_buka'=>'07:30:00','jam_tutup'=>'16:30:00','keterangan'=>'Hari normal'],
            ['hari'=>'Jumat','jam_buka'=>'07:30:00','jam_tutup'=>'20:20:00','keterangan'=>'Extended hours'],
            ['hari'=>'Sabtu','jam_buka'=>'08:00:00','jam_tutup'=>'20:20:00','keterangan'=>'Weekend schedule'],
            ['hari'=>'Minggu','jam_buka'=>'00:00:00','jam_tutup'=>'00:00:00','keterangan'=>'Lab tutup'],
        ];

        foreach ($rows as $r) {
            DB::table('jam_operasional_labs')->updateOrInsert(['hari'=>$r['hari']], $r + ['created_at'=>now(),'updated_at'=>now()]);
        }
    }
}