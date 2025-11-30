<?php

namespace App\Filament\Resources\JadwalLabs\Pages;

use App\Filament\Resources\JadwalLabs\JadwalLabResource;
use App\Models\JadwalLab;
use App\Models\Ruangan;
use App\Models\Semester;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\Facades\DB;
use Filament\Schemas\Schema;
use App\Enums\SemesterStatus; // Tambahkan ini

class UploadJadwalLab extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = JadwalLabResource::class;
    protected static ?string $title = 'Upload Jadwal Lab';
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
                    ->label("Upload CSV Jadwal")
                    ->acceptedFileTypes(['text/csv', 'application/csv', 'text/plain'])
                    ->directory('csv/jadwal')
                    ->disk('local')
                    ->maxSize(5120)
                    ->required(),
            ])
            ->statePath('data');
    }

    public function submit()
    {
        try {
            $data = $this->form->getState();

            if (empty($data['csv_file'])) {
                Notification::make()
                    ->danger()
                    ->title('File tidak valid!')
                    ->send();
                return;
            }

            $filePath = $data['csv_file'];
            $fullPath = Storage::disk('local')->path($filePath);

            if (!file_exists($fullPath)) {
                Notification::make()
                    ->danger()
                    ->title('File tidak ditemukan!')
                    ->send();
                return;
            }

            // Ganti query semester pakai 'status' bukan 'is_active'
            // Asumsi: SemesterStatus::Aktif adalah status untuk semester aktif
            $idSemester = Semester::where('status', SemesterStatus::Aktif)->value('id_semester');

            if (!$idSemester) {
                Notification::make()
                    ->danger()
                    ->title('Semester aktif tidak ditemukan!')
                    ->body('Pastikan ada semester dengan status Aktif')
                    ->send();
                return;
            }

            $ruanganMapping = [
                'Lab.Das.A (G.2)' => 'Lab Dasar A',
                'Lab.Das.B (G.2)' => 'Lab Dasar B',
                'Lab.Das.C (G.2)' => 'Lab Dasar C',
                'Lab.Das.D (G.2)' => 'Lab Dasar D',
                'Lab.Das.E (G.2)' => 'Lab Dasar E',
                'Lab.Das.F (F.1)' => 'Lab Dasar F',
                'Lab.Lanjut A (G.4)' => 'Lab Lanjut A',
                'Lab.Lanjut B (G.4)' => 'Lab Lanjut B',
                'Lab.Lanjut C (G.4)' => 'Lab Lanjut C',
                'Lab.Lanjut D (G.4)' => 'Lab Lanjut D',
                'Lab Gambar DKV (B.3)' => 'Lab IAC',
                'Lab.IAC' => 'Lab IAC',
                'Lab.Kom.Akt (G.3)' => 'Lab Kom Akt',
                'Lab.Jar.Kom Lanjut(G.3)' => 'Lab Jarkom',
                'Lab.Elektronika (G.3)' => 'Lab Elektronika',
            ];



            // Cache semua ruangan pakai nama_ruangan sebagai key
            $ruanganMap = Ruangan::pluck('id_ruangan', 'nama_ruangan')->toArray();

            $file = fopen($fullPath, 'r');
            $header = fgetcsv($file, 0, ';'); // Skip header

            $imported = 0;
            $skipped = 0;
            $errors = [];
            $batchData = [];
            $batchSize = 100;
            $newMatakuliah = [];

            DB::beginTransaction();

            while (($row = fgetcsv($file, 0, ';')) !== false) {
                // Skip baris kosong
                if (empty(array_filter($row))) {
                    continue;
                }

                // Pastikan minimal 8 kolom
                if (count($row) < 8) {
                    $skipped++;
                    continue;
                }

                $hari           = $row[0] ?? null;
                $waktu          = $row[1] ?? null;
                $ruang          = $row[2] ?? null;
                $kode_mk        = $row[3] ?? null;
                $nama_mk        = $row[4] ?? null;
                $sks            = $row[5] ?? null;
                $kelas          = $row[6] ?? null;
                $dosen          = $row[7] ?? null;

                $jumlah_mhs_csv = $row[8] ?? null;


                // Parse waktu
                $waktuParts = explode('-', trim($waktu));
                if (count($waktuParts) !== 2) {
                    $skipped++;
                    continue;
                }

                [$jam_mulai, $jam_selesai] = $waktuParts;

                // Cari ruangan dari map
                $namaRuanganCSV = trim($ruang);
                $namaRuanganDB = $ruanganMapping[$namaRuanganCSV] ?? $namaRuanganCSV;
                $idRuangan = $ruanganMap[$namaRuanganDB] ?? null;

                if (!$idRuangan) {
                    $skipped++;
                    $errors[] = "Ruangan '$namaRuanganCSV' → '$namaRuanganDB' tidak ditemukan di database";
                    continue;
                }

                $jumlahMahasiswa = is_numeric($jumlah_mhs_csv) ? (int) $jumlah_mhs_csv : 30;

                $jumlahAsisten = $jumlahMahasiswa >= 20 ? 2 : 1;

                $kodeMK = trim($kode_mk);
                $namaMK = trim($nama_mk);
                $sksValue = trim($sks);
                $kelasValue = trim($kelas);
                $dosenValue = trim($dosen);

                $sksParsed = 2;
                if (strpos($sksValue, '/') !== false) {
                    $sksParsed = (int) explode('/', $sksValue)[0];
                }

                $matakuliahExists = DB::table('matakuliahs')
                    ->where('kode_mk', $kodeMK)
                    ->exists();

                if (!$matakuliahExists) {
                    DB::table('matakuliahs')->insert([
                        'kode_mk' => $kodeMK,
                        'nama_mk' => $namaMK,
                        'sks' => $sksParsed,
                        'kelas' => $kelasValue,
                        'dosen' => $dosenValue,
                        'semester_berjalan' => $idSemester,
                        'deskripsi' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $newMatakuliah[] = "$kodeMK - $namaMK";
                }

                $batchData[] = [
                    'id_semester' => $idSemester,
                    'hari' => trim($hari),
                    'jam_mulai' => trim($jam_mulai),
                    'jam_selesai' => trim($jam_selesai),
                    'id_ruangan' => $idRuangan,
                    'kode_mk' => $kodeMK,
                    'jumlah_mahasiswa' => $jumlahMahasiswa,
                    'jumlah_asisten_dibutuhkan' => $jumlahAsisten,
                    'prioritas' => 'Sedang',
                    'status' => 'Draft',
                    'keterangan' => "Kelas: $kelasValue | Dosen: $dosenValue",
                    'created_at' => now(),
                    'updated_at' => now(),
                ];


                $imported++;

                // Batch insert setiap 100 data
                if (count($batchData) >= $batchSize) {
                    JadwalLab::insert($batchData);
                    $batchData = [];
                }
            }

            // Insert sisa data
            if (!empty($batchData)) {
                JadwalLab::insert($batchData);
            }

            fclose($file);
            DB::commit();

            // Hapus file setelah berhasil
            Storage::disk('local')->delete($filePath);

            $message = "{$imported} data berhasil diimport";
            if ($skipped > 0) {
                $message .= ", {$skipped} data dilewati";
            }
            if (!empty($newMatakuliah)) {
                $message .= "\n" . count($newMatakuliah) . " matakuliah baru dibuat";
            }


            Notification::make()
                ->success()
                ->title('Jadwal Lab berhasil diunggah!')
                ->body($message)
                ->send();

            // Tampilkan warning jika ada error (max 10)
            if (!empty($errors)) {
                $errorMessage = implode("\n", array_slice($errors, 0, 10));
                if (count($errors) > 10) {
                    $errorMessage .= "\n... dan " . (count($errors) - 10) . " error lainnya";
                }

                Notification::make()
                    ->warning()
                    ->title('Beberapa data dilewati')
                    ->body($errorMessage)
                    ->persistent()
                    ->send();
            }

            if (!empty($newMatakuliah) && count($newMatakuliah) <= 10) {
                Notification::make()
                    ->info()
                    ->title('Matakuliah baru dibuat')
                    ->body(implode("\n", $newMatakuliah))
                    ->persistent()
                    ->send();
            }


            // Reset form
            $this->form->fill();
        } catch (\Exception $e) {
            DB::rollBack();

            Notification::make()
                ->danger()
                ->title('Terjadi kesalahan!')
                ->body($e->getMessage())
                ->send();
        }
    }
}
