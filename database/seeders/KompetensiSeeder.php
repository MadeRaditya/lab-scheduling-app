<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Enums\KategoriKompetensi;
use App\Enums\LevelKompetensi;

class KompetensiSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'nama_kompetensi'=> 'Programming (VB.net, Phyton, Database)',
                'deskripsi' => 'Kompetensi dalam pengembangan software digital.',
                'kategori'=> KategoriKompetensi::Ilmu_Komputer,
                'level'=> LevelKompetensi::Lanjut
            ],
            [
                'nama_kompetensi'=> 'Web Programming',
                'deskripsi' => 'Kompetensi dalam pengembangan aplikasi web menggunakan teknologi frontend dan backend untuk membangun sistem berbasis internet.',
                'kategori'=> KategoriKompetensi::Ilmu_Komputer,
                'level'=> LevelKompetensi::Lanjut
            ],
            [
                'nama_kompetensi'=> 'Mobile Programming (Android)',
                'deskripsi' => 'Kemampuan membuat aplikasi mobile berbasis Android dengan memanfaatkan berbagai framework dan bahasa pemrograman.',
                'kategori'=> KategoriKompetensi::Ilmu_Komputer,
                'level'=> LevelKompetensi::Menengah
            ],
            [
                'nama_kompetensi'=> 'Desain Grafis dan 3D Animation',
                'deskripsi' => 'Keahlian dalam membuat desain visual, ilustrasi, serta animasi 3D untuk kebutuhan media digital dan industri kreatif.',
                'kategori'=> KategoriKompetensi::Desain,
                'level'=> LevelKompetensi::Menengah
            ],
            [
                'nama_kompetensi'=> 'AI (Matlab, Orange, Rapid Miner)',
                'deskripsi' => 'Kompetensi dalam analisis data dan pemodelan kecerdasan buatan menggunakan tools seperti Matlab, Orange, dan Rapid Miner.',
                'kategori'=> KategoriKompetensi::Ilmu_Komputer,
                'level'=> LevelKompetensi::Lanjut
            ],
            [
                'nama_kompetensi'=> 'Jaringan Komputer',
                'deskripsi' => 'Pemahaman arsitektur, konfigurasi, dan pengelolaan jaringan komputer untuk komunikasi data yang aman dan stabil.',
                'kategori'=> KategoriKompetensi::Ilmu_Komputer,
                'level'=> LevelKompetensi::Menengah
            ],
            [
                'nama_kompetensi'=> 'Elektronika',
                'deskripsi' => 'Dasar-dasar elektronika termasuk rangkaian, sensor, dan komponen elektronik dalam sistem teknologi.',
                'kategori'=> KategoriKompetensi::Ilmu_Komputer,
                'level'=> LevelKompetensi::Dasar
            ],
            [
                'nama_kompetensi'=> 'Manajemen Keuangan',
                'deskripsi' => 'Keahlian dalam mengelola keuangan, menganalisis laporan keuangan, dan penganggaran.',
                'kategori'=> KategoriKompetensi::Ekonomi_Bisnis,
                'level'=> LevelKompetensi::Menengah
            ],
            [
                'nama_kompetensi'=> 'Manajemen Pemasaran',
                'deskripsi' => 'Kemampuan merancang strategi pemasaran, memahami perilaku konsumen, serta mengelola promosi.',
                'kategori'=> KategoriKompetensi::Ekonomi_Bisnis,
                'level'=> LevelKompetensi::Dasar
            ],
            [
                'nama_kompetensi'=> 'Manajemen SDM',
                'deskripsi' => 'Kompetensi dalam pengelolaan sumber daya manusia, mulai dari rekrutmen hingga pengembangan karyawan.',
                'kategori'=> KategoriKompetensi::Ekonomi_Bisnis,
                'level'=> LevelKompetensi::Dasar
            ],
            [
                'nama_kompetensi'=> 'Akuntansi',
                'deskripsi' => 'Pemahaman pencatatan, pengelolaan, dan pelaporan transaksi keuangan sesuai standar akuntansi.',
                'kategori'=> KategoriKompetensi::Ekonomi_Bisnis,
                'level'=> LevelKompetensi::Menengah
            ],
            [
                'nama_kompetensi'=> 'Science Data',
                'deskripsi' => 'Kompetensi dalam pengolahan data, analisis statistik, dan metode data science untuk pengambilan keputusan berbasis data.',
                'kategori'=> KategoriKompetensi::Ilmu_Komputer,
                'level'=> LevelKompetensi::Lanjut
            ],
        ];

        foreach ($rows as $r) {
            DB::table('kompetensis')->updateOrInsert(
                ['nama_kompetensi' => $r['nama_kompetensi']],
                [
                    ...$r,
                    'kategori' => $r['kategori']->value,
                    'level'    => $r['level']->value,   
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
