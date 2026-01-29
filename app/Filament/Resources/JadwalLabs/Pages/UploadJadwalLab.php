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
use Filament\Actions\Action;
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
    protected static ?string $title = "Upload Jadwal Lab Full";
    protected string $view = "filament.resources.jadwal-labs.pages.upload-jadwal-lab";

    public ?array $data = [];

    public bool $isImporting = false;
    public int $totalRows = 0;
    public int $processedRows = 0;
    public string $currentFilePath = "";
    public int $successCount = 0;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FileUpload::make("csv_file")
                    ->label("Upload CSV Jadwal (Full Data)")
                    ->helperText(
                        "Format: tahun;jenis_sem;nama_ruang;lokasi;kapasitas;kode_mk;nama_mk;sks;kelas;dosen;hari;jam_mulai;jam_selesai;jml_mhs;prioritas;status;kompetensi(|)",
                    )
                    ->acceptedFileTypes([
                        "text/csv",
                        "application/csv",
                        "text/plain",
                    ])
                    ->directory("csv/jadwal")
                    ->disk("local")
                    ->required(),
            ])
            ->statePath("data");
    }

    public function startImport()
    {
        $data = $this->form->getState();
        if (empty($data["csv_file"])) {
            return;
        }

        $filePath = $data["csv_file"];
        $fullPath = Storage::disk("local")->path($filePath);

        if (!file_exists($fullPath)) {
            Notification::make()
                ->danger()
                ->title("File tidak ditemukan!")
                ->send();
            return;
        }

        $file = new \SplFileObject($fullPath, "r");
        $file->seek(PHP_INT_MAX);
        $this->totalRows = $file->key();

        $this->currentFilePath = $filePath;
        $this->processedRows = 0;
        $this->successCount = 0;
        $this->isImporting = true;
    }

    public function importBatch()
    {
        if (!$this->isImporting) {
            return;
        }

        $fullPath = Storage::disk("local")->path($this->currentFilePath);
        $file = new \SplFileObject($fullPath, "r");

        $file->setFlags(\SplFileObject::READ_CSV);
        $file->setCsvControl(";");

        $file->seek($this->processedRows);

        if ($this->processedRows === 0) {
            $file->current();
            $file->next();
            $this->processedRows++;
        }

        $batchSize = 50;
        $count = 0;

        DB::beginTransaction();
        try {
            while (!$file->eof() && $count < $batchSize) {
                $row = $file->current();

                if (empty($row) || count($row) < 17) {
                    $file->next();
                    $this->processedRows++;
                    continue;
                }

                $csvTahun = trim($row[0]);
                $csvJenisSem = trim($row[1]);
                $csvRuangan = trim($row[2]);
                $csvLokasi = trim($row[3]);
                $csvKapasitas = trim($row[4]);
                $csvKodeMK = trim($row[5]);
                $csvNamaMK = trim($row[6]);
                $csvSKS = trim($row[7]);
                $csvKelas = trim($row[8]);
                $csvDosen = trim($row[9]);
                $csvHari = trim($row[10]);
                $csvMulai = trim($row[11]);
                $csvSelesai = trim($row[12]);
                $csvJmlMhs = trim($row[13]);
                $csvPrioritas = trim($row[14]);
                $csvStatusJadwal = trim($row[15]);
                $csvKompetensi = $row[16] ?? null;

                $jenisEnum =
                    SemesterJenis::tryFrom($csvJenisSem) ??
                    SemesterJenis::Ganjil;

                $semester = Semester::firstOrCreate(
                    ["tahun_ajaran" => $csvTahun, "jenis" => $jenisEnum],
                    [
                        "nama_semester" => "Semester {$csvJenisSem} {$csvTahun}",
                        "status" => SemesterStatus::Aktif,
                        "tanggal_mulai" => now(),
                        "tanggal_selesai" => now()->addMonths(6),
                    ],
                );

                $kategoriEnum = RuanganKategori::LABORATORIUM;
                if (stripos($csvRuangan, "Dasar") !== false) {
                    $kategoriEnum = RuanganKategori::LAB_DASAR;
                } elseif (stripos($csvRuangan, "Lanjut") !== false) {
                    $kategoriEnum = RuanganKategori::LAB_LANJUT;
                }

                $ruangan = Ruangan::updateOrCreate(
                    ["nama_ruangan" => $csvRuangan],
                    [
                        "lokasi"=>$csvLokasi,
                        "kapasitas" => (int) $csvKapasitas,
                        "kategori" => $kategoriEnum,
                        "status" => RuanganStatus::AKTIF,
                    ],
                );

                $matakuliah = Matakuliah::updateOrCreate(
                    [
                        "kode_mk" => $csvKodeMK,
                        "kelas" => $csvKelas,
                        "semester_berjalan" => $semester->id_semester,
                    ],
                    [
                        "nama_mk" => $csvNamaMK,
                        "sks" => (int) $csvSKS,
                        "dosen" => $csvDosen,
                    ],
                );

                if (!empty($csvKompetensi)) {
                    $listKomp = explode("|", $csvKompetensi);
                    $kompetensiIds = [];
                    foreach ($listKomp as $namaKomp) {
                        $namaKomp = trim($namaKomp);
                        if (empty($namaKomp)) {
                            continue;
                        }
                        $komp = Kompetensi::firstOrCreate(
                            ["nama_kompetensi" => $namaKomp],
                            ["deskripsi" => "Auto-imported"],
                        );
                        $kompetensiIds[] = $komp->id_kompetensi;
                    }
                    $matakuliah
                        ->kompetensis()
                        ->syncWithoutDetaching($kompetensiIds);
                }

                $jmlMhsInt = (int) $csvJmlMhs;
                $asistenButuh = $jmlMhsInt >= 20 ? 2 : 1;

                $isConflict = JadwalLab::where('hari', $csvHari)
                    ->where('id_ruangan', $ruangan->id_ruangan)
                    ->where('id_semester', $semester->id_semester)
                    ->where(function ($q) use ($csvMulai, $csvSelesai) {
                        $q->where('jam_mulai', '<', $csvSelesai)
                            ->where('jam_selesai', '>', $csvMulai);
                    })
                    ->exists();

                if ($isConflict) {
                    Notification::make()
                        ->warning()
                        ->title("Bentrok Ruangan Baris {$this->processedRows}")
                        ->body("Ruang {$csvRuangan} penuh pada {$csvHari} {$csvMulai}-{$csvSelesai}")
                        ->send();

                    $file->next();
                    continue;
                }

                JadwalLab::create([
                    "id_semester" => $semester->id_semester,
                    "matakuliah_id" => $matakuliah->id,
                    "id_ruangan" => $ruangan->id_ruangan,
                    "hari" => $csvHari,
                    "jam_mulai" => $csvMulai,
                    "jam_selesai" => $csvSelesai,
                    "jumlah_mahasiswa" => $jmlMhsInt,
                    "jumlah_asisten_dibutuhkan" => $asistenButuh,
                    "prioritas" => $csvPrioritas,
                    "status" => $csvStatusJadwal,
                    "keterangan" => "Dosen: {$csvDosen}",
                ]);

                $count++;
                $this->successCount++;
                $this->processedRows++;
                $file->next();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()
                ->warning()
                ->title("Error pada baris " . $this->processedRows)
                ->body($e->getMessage())
                ->send();
        }

        if ($file->eof() || $this->processedRows >= $this->totalRows) {
            $this->isImporting = false;
            Storage::disk("local")->delete($this->currentFilePath);
            $this->form->fill();

            Notification::make()
                ->success()
                ->title("Import Selesai")
                ->body("{$this->successCount} jadwal berhasil diimport.")
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_template')
                ->label('Download Template Jadwal')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => 'attachment; filename="template_jadwal_lab.csv"',
                    ];

                    $columns = [
                        'Tahun', 'Jenis_sem', 'Nama_ruang', 'Lokasi', 'Kapasitas', 
                        'KodeMK', 'NamaMK', 'SKS', 'Kelas', 'Dosen', 
                        'Hari', 'Jam_mulai', 'Jam_selesai', 'JmlMhs', 'Prioritas', 'Status', 'Kompetensi'
                    ];
                    
                    $sample = [
                        '2025/2026', 'Ganjil', 'Lab RPL', 'G.2', '40', 
                        'IF101', 'Pemrograman Web', '3', 'A', 'Pak Budi', 
                        'Senin', '08:00', '10:00', '35', 'Tinggi', 'Draft', 'PHP|Laravel'
                    ];

                    $callback = function () use ($columns, $sample) {
                        $file = fopen('php://output', 'w');
                        fputcsv($file, $columns, ';');
                        fputcsv($file, $sample, ';');
                        fclose($file);
                    };

                    return response()->stream($callback, 200, $headers);
                }),
        ];
    }
}
