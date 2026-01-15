<?php

namespace App\Filament\Resources\Matakuliahs\Pages;

use App\Enums\SemesterJenis;
use App\Enums\SemesterStatus;
use App\Filament\Resources\Matakuliahs\MatakuliahResource;
use App\Models\Kompetensi;
use App\Models\Matakuliah;
use App\Models\Semester;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UploadMatakuliah extends Page implements HasForms
{
    use InteractsWithForms;

    public function getView(): string
    {
        return "filament.resources.matakuliahs.pages.upload-matakuliah";
    }
    protected static string $resource = MatakuliahResource::class;
    protected static ?string $title = "Upload Data Matakuliah";

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
                    ->label("Upload CSV Matakuliah")
                    ->helperText(
                        "Format: KodeMK;NamaMK;SKS;Kelas;Dosen;Tahun_Sem;Jenis_Sem;Kompetensi(|);Deskripsi",
                    )
                    ->acceptedFileTypes([
                        "text/csv",
                        "application/csv",
                        "text/plain",
                    ])
                    ->directory("csv/matakuliah")
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

                if (empty($row) || count($row) < 5) {
                    $file->next();
                    $this->processedRows++;
                    continue;
                }

                $csvKode = trim($row[0]);
                $csvNama = trim($row[1]);
                $csvSks = (int) trim($row[2]);
                $csvKelas = trim($row[3]);
                $csvDosen = trim($row[4]);
                $csvTahun = isset($row[5]) ? trim($row[5]) : date("Y");
                $csvJenis = isset($row[6]) ? trim($row[6]) : "Ganjil";
                $csvKompetensi = isset($row[7]) ? trim($row[7]) : null;
                $csvDeskripsi = isset($row[8]) ? trim($row[8]) : null;

                $jenisEnum =
                    SemesterJenis::tryFrom($csvJenis) ?? SemesterJenis::Ganjil;

                $semester = Semester::firstOrCreate(
                    ["tahun_ajaran" => $csvTahun, "jenis" => $jenisEnum],
                    [
                        "nama_semester" => "Semester {$csvJenis} {$csvTahun}",
                        "status" => SemesterStatus::Aktif,
                        "tanggal_mulai" => now(),
                        "tanggal_selesai" => now()->addMonths(6),
                    ],
                );

                $matakuliah = Matakuliah::updateOrCreate(
                    ["kode_mk" => $csvKode],
                    [
                        "nama_mk" => $csvNama,
                        "sks" => $csvSks,
                        "kelas" => $csvKelas,
                        "dosen" => $csvDosen,
                        "semester_berjalan" => $semester->id_semester,
                        "deskripsi" => $csvDeskripsi,
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
                            ["deskripsi" => "Auto-imported via MK CSV"],
                        );
                        $kompetensiIds[] = $komp->id_kompetensi;
                    }

                    $matakuliah
                        ->kompetensis()
                        ->syncWithoutDetaching($kompetensiIds);
                }

                $count++;
                $this->successCount++;
                $this->processedRows++;
                $file->next();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }

        if ($file->eof() || $this->processedRows >= $this->totalRows) {
            $this->isImporting = false;
            Storage::disk("local")->delete($this->currentFilePath);
            $this->form->fill();

            Notification::make()
                ->success()
                ->title("Import Selesai")
                ->body("{$this->successCount} matakuliah berhasil diproses.")
                ->send();
        }
    }
}
