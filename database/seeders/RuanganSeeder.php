<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Enums\RuanganKategori;
use App\Enums\RuanganStatus;

class RuanganSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            // Gedung G Lantai 2
            ['nama_ruangan' => 'Lab Dasar A', 'gedung' => 'G', 'lantai' => 2, 'kapasitas' => 40, 'kategori' => RuanganKategori::LAB_DASAR, 'status' => RuanganStatus::AKTIF],
            ['nama_ruangan' => 'Lab Dasar B', 'gedung' => 'G', 'lantai' => 2, 'kapasitas' => 40, 'kategori' => RuanganKategori::LAB_DASAR, 'status' => RuanganStatus::AKTIF],
            ['nama_ruangan' => 'Lab Dasar C', 'gedung' => 'G', 'lantai' => 2, 'kapasitas' => 40, 'kategori' => RuanganKategori::LAB_DASAR, 'status' => RuanganStatus::AKTIF],
            ['nama_ruangan' => 'Lab Dasar D', 'gedung' => 'G', 'lantai' => 2, 'kapasitas' => 40, 'kategori' => RuanganKategori::LAB_DASAR, 'status' => RuanganStatus::AKTIF],
            ['nama_ruangan' => 'Lab Dasar E', 'gedung' => 'G', 'lantai' => 2, 'kapasitas' => 40, 'kategori' => RuanganKategori::LAB_DASAR, 'status' => RuanganStatus::AKTIF],

            // Gedung G Lantai 3
            ['nama_ruangan' => 'Lab Kom Akt', 'gedung' => 'G', 'lantai' => 3, 'kapasitas' => 40, 'kategori' => RuanganKategori::LABORATORIUM, 'status' => RuanganStatus::AKTIF],
            ['nama_ruangan' => 'Lab Jarkom', 'gedung' => 'G', 'lantai' => 3, 'kapasitas' => 40, 'kategori' => RuanganKategori::LABORATORIUM, 'status' => RuanganStatus::AKTIF],
            ['nama_ruangan' => 'Lab Elektronika', 'gedung' => 'G', 'lantai' => 3, 'kapasitas' => 40, 'kategori' => RuanganKategori::LABORATORIUM, 'status' => RuanganStatus::AKTIF],

            // Gedung G Lantai 4
            ['nama_ruangan' => 'Lab Lanjut A', 'gedung' => 'G', 'lantai' => 4, 'kapasitas' => 40, 'kategori' => RuanganKategori::LAB_LANJUT, 'status' => RuanganStatus::AKTIF],
            ['nama_ruangan' => 'Lab Lanjut B', 'gedung' => 'G', 'lantai' => 4, 'kapasitas' => 40, 'kategori' => RuanganKategori::LAB_LANJUT, 'status' => RuanganStatus::AKTIF],
            ['nama_ruangan' => 'Lab Lanjut C', 'gedung' => 'G', 'lantai' => 4, 'kapasitas' => 40, 'kategori' => RuanganKategori::LAB_LANJUT, 'status' => RuanganStatus::AKTIF],
            ['nama_ruangan' => 'Lab Lanjut D', 'gedung' => 'G', 'lantai' => 4, 'kapasitas' => 40, 'kategori' => RuanganKategori::LAB_LANJUT, 'status' => RuanganStatus::AKTIF],

            // Lab Tambahan
            ['nama_ruangan' => 'Lab Dasar F', 'gedung' => 'F', 'lantai' => 1, 'kapasitas' => 40, 'kategori' => RuanganKategori::LAB_DASAR, 'status' => RuanganStatus::AKTIF],
            ['nama_ruangan' => 'Lab IAC', 'gedung' => 'B', 'lantai' => 1, 'kapasitas' => 40, 'kategori' => RuanganKategori::LABORATORIUM, 'status' => RuanganStatus::AKTIF],
            ['nama_ruangan' => 'Lab Gambar DKV', 'gedung' => 'B', 'lantai' => 3, 'kapasitas' => 40, 'kategori' => RuanganKategori::LABORATORIUM, 'status' => RuanganStatus::AKTIF],
            ['nama_ruangan' => 'Lab Dasar DKV', 'gedung' => 'B', 'lantai' => 3, 'kapasitas' => 40, 'kategori' => RuanganKategori::LABORATORIUM, 'status' => RuanganStatus::AKTIF],
        ];

        foreach ($rows as $r) {
            DB::table('ruangans')->updateOrInsert(
                ['nama_ruangan' => $r['nama_ruangan']],
                [
                    ...$r,
                    'kategori' => $r['kategori']->value,
                    'status'   => $r['status']->value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
