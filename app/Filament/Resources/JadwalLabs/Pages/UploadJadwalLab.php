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
                        "Format: tahun;jenis;ruang;gedung;lantai;kapasitas;kode_mk;nama_mk;sks;kelas;dosen;hari;mulai;selesai;jml_mhs;prioritas;status;kompetensi(|)",
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
                $csvGedung = trim($row[3]);
                $csvLantai = trim($row[4]);
                $csvKapasitas = trim($row[5]);
                $csvKodeMK = trim($row[6]);
                $csvNamaMK = trim($row[7]);
                $csvSKS = trim($row[8]);
                $csvKelas = trim($row[9]);
                $csvDosen = trim($row[10]);
                $csvHari = trim($row[11]);
                $csvMulai = trim($row[12]);
                $csvSelesai = trim($row[13]);
                $csvJmlMhs = trim($row[14]);
                $csvPrioritas = trim($row[15]);
                $csvStatusJadwal = trim($row[16]);
                $csvKompetensi = $row[17] ?? null;

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
                        "gedung" => $csvGedung,
                        "lantai" => (int) $csvLantai,
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
}
