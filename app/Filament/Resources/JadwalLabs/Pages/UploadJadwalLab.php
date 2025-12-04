<?php

namespace App\Filament\Resources\JadwalLabs\Pages;

use App\Filament\Resources\JadwalLabs\JadwalLabResource;
use App\Models\JadwalLab;
use App\Models\Ruangan;
use App\Models\Semester;
use App\Models\Matakuliah;
use App\Models\Kompetensi;
use App\Enums\SemesterJenis;
use App\Enums\SemesterStatus;
use App\Enums\RuanganKategori;
use App\Enums\RuanganStatus;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\Facades\DB;
use Filament\Schemas\Schema;

class UploadJadwalLab extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = JadwalLabResource::class;
    protected static ?string $title = 'Upload Jadwal Lab Full';
    protected string $view = 'filament.resources.jadwal-labs.pages.upload-jadwal-lab';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FileUpload::make('csv_file')
                    ->label("Upload CSV Jadwal (Full Data)")
                    ->helperText('Format: tahun;jenis;ruang;gedung;lantai;kapasitas;kode_mk;nama_mk;sks;kelas;dosen;hari;mulai;selesai;jml_mhs;prioritas;status;kompetensi')
                    ->acceptedFileTypes(['text/csv', 'application/csv', 'text/plain'])
                    ->directory('csv/jadwal')
                    ->disk('local')
                    ->required(),
            ])
            ->statePath('data');
    }

    public function submit()
    {
        $data = $this->form->getState();

        if (empty($data['csv_file'])) return;

        $filePath = $data['csv_file'];
        $fullPath = Storage::disk('local')->path($filePath);

        if (!file_exists($fullPath)) {
            Notification::make()->danger()->title('File tidak ditemukan!')->send();
            return;
        }

        $file = fopen($fullPath, 'r');
        fgetcsv($file, 0, ';'); 

        DB::beginTransaction();

        try {
            $countImport = 0;

            while (($row = fgetcsv($file, 0, ';')) !== false) {
                if (empty(array_filter($row)) || count($row) < 17) continue;

                $csvTahun       = trim($row[0]);
                $csvJenisSem    = trim($row[1]);
                $csvRuangan     = trim($row[2]);
                $csvGedung      = trim($row[3]);
                $csvLantai      = trim($row[4]); 
                $csvKapasitas   = trim($row[5]); 
                $csvKodeMK      = trim($row[6]);
                $csvNamaMK      = trim($row[7]);
                $csvSKS         = trim($row[8]);
                $csvKelas       = trim($row[9]);
                $csvDosen       = trim($row[10]);
                $csvHari        = trim($row[11]);
                $csvMulai       = trim($row[12]);
                $csvSelesai     = trim($row[13]);
                $csvJmlMhs      = trim($row[14]);
                $csvPrioritas   = trim($row[15]); 
                $csvStatusJadwal= trim($row[16]); 
                $csvKompetensi  = $row[17] ?? null;

                $jenisEnum = SemesterJenis::tryFrom($csvJenisSem) ?? SemesterJenis::Ganjil;
                
                $semester = Semester::firstOrCreate(
                    ['tahun_ajaran' => $csvTahun, 'jenis' => $jenisEnum],
                    [
                        'nama_semester' => "Semester {$csvJenisSem} {$csvTahun}",
                        'status' => SemesterStatus::Aktif, 
                        'tanggal_mulai' => now(),
                        'tanggal_selesai' => now()->addMonths(6),
                    ]
                );

                $kategoriEnum = RuanganKategori::LABORATORIUM;
                if (stripos($csvRuangan, 'Dasar') !== false) $kategoriEnum = RuanganKategori::LAB_DASAR;
                elseif (stripos($csvRuangan, 'Lanjut') !== false) $kategoriEnum = RuanganKategori::LAB_LANJUT;

                $ruangan = Ruangan::updateOrCreate(
                    ['nama_ruangan' => $csvRuangan],
                    [
                        'gedung' => $csvGedung,
                        'lantai' => (int) $csvLantai,       
                        'kapasitas' => (int) $csvKapasitas, 
                        'kategori' => $kategoriEnum,
                        'status' => RuanganStatus::AKTIF,
                    ]
                );

                $matakuliah = Matakuliah::updateOrCreate(
                    ['kode_mk' => $csvKodeMK],
                    [
                        'nama_mk' => $csvNamaMK,
                        'sks' => (int) $csvSKS,
                        'kelas' => $csvKelas,
                        'dosen' => $csvDosen,
                        'semester_berjalan' => $semester->id_semester,
                    ]
                );

                if (!empty($csvKompetensi)) {
                    $listKomp = explode(',', $csvKompetensi);
                    $kompetensiIds = [];
                    foreach ($listKomp as $namaKomp) {
                        $namaKomp = trim($namaKomp);
                        if(empty($namaKomp)) continue;
                        $komp = Kompetensi::firstOrCreate(
                            ['nama_kompetensi' => $namaKomp],
                            ['deskripsi' => 'Auto-imported']
                        );
                        $kompetensiIds[] = $komp->id_kompetensi;
                    }
                    $matakuliah->kompetensis()->syncWithoutDetaching($kompetensiIds);
                }

                $jmlMhsInt = (int) $csvJmlMhs;
                $asistenButuh = ($jmlMhsInt >= 20) ? 2 : 1;

                JadwalLab::create([
                    'id_semester' => $semester->id_semester,
                    'kode_mk' => $matakuliah->kode_mk,
                    'id_ruangan' => $ruangan->id_ruangan,
                    'hari' => $csvHari,
                    'jam_mulai' => $csvMulai,
                    'jam_selesai' => $csvSelesai,
                    'jumlah_mahasiswa' => $jmlMhsInt,
                    'jumlah_asisten_dibutuhkan' => $asistenButuh,
                    'prioritas' => $csvPrioritas, 
                    'status' => $csvStatusJadwal, 
                    'keterangan' => "Dosen: {$csvDosen}",
                ]);

                $countImport++;
            }

            DB::commit();
            fclose($file);
            Storage::disk('local')->delete($filePath);

            Notification::make()->success()
                ->title('Import Sukses')
                ->body("{$countImport} jadwal berhasil dibuat dengan data dinamis.")
                ->send();

            $this->form->fill();

        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($file)) fclose($file);
            Notification::make()->danger()->title('Gagal')->body($e->getMessage())->persistent()->send();
        }
    }
}