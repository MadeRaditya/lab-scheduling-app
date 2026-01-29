<?php

namespace App\Filament\Resources\Asistens\Pages;

use App\Enums\JurusanAsisten;
use App\Filament\Resources\Asistens\AsistenResource;
use App\Models\Asisten;
use App\Models\Kompetensi;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UploadAsisten extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = AsistenResource::class;
    protected static ?string $title = "Upload Data Asisten";

    public ?array $data = [];

    public bool $isImporting = false;
    public int $totalRows = 0;
    public int $processedRows = 0;
    public string $currentFilePath = "";
    public int $successCount = 0;

    public function getView(): string
    {
        return "filament.resources.asistens.pages.upload-asisten";
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FileUpload::make("csv_file")
                    ->label("Upload CSV Asisten")
                    ->helperText(
                        "Format: Nama Asisten;NPM;Jurusan;Email;NoHP;Status Aktif;kompetensi(|)",
                    )
                    ->acceptedFileTypes([
                        "text/csv",
                        "application/csv",
                        "text/plain",
                    ])
                    ->directory("csv/asisten")
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

        $this->currentFilePath = $data["csv_file"];
        $fullPath = Storage::disk("local")->path($this->currentFilePath);

        if (!file_exists($fullPath)) {
            Notification::make()
                ->danger()
                ->title("File tidak ditemukan")
                ->send();
            return;
        }

        $file = new \SplFileObject($fullPath);
        $file->setFlags(
            \SplFileObject::READ_CSV |
                \SplFileObject::READ_AHEAD |
                \SplFileObject::SKIP_EMPTY,
        );
        $file->setCsvControl(";");

        $total = 0;
        foreach ($file as $row) {
            if (!empty($row) && count($row) >= 2) {
                $total++;
            }
        }

        $this->totalRows = max(0, $total - 1);
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
        $file = new \SplFileObject($fullPath);
        $file->setFlags(
            \SplFileObject::READ_CSV |
                \SplFileObject::READ_AHEAD |
                \SplFileObject::SKIP_EMPTY,
        );
        $file->setCsvControl(";");

        $seekLine = $this->processedRows + 1;

        try {
            $file->seek($seekLine);
        } catch (\RuntimeException $e) {
            $this->processedRows = $this->totalRows;
            return;
        }

        DB::beginTransaction();

        try {
            $batchSize = 25;
            $count = 0;

            while ($count < $batchSize) {
                $row = $file->current();

                if (empty($row) && $file->eof()) {
                    break;
                }

                if (empty($row) || count($row) < 4) {
                    $this->processedRows++;
                    $file->next();
                    if ($file->eof()) {
                        break;
                    }
                    continue;
                }

                $csvNama = trim($row[0]);
                $csvNpm = trim($row[1]);
                $csvJurusan = trim($row[2]);
                $csvEmail = trim($row[3]);
                $csvNoHp = $row[4] ?? null;
                $csvStatus = trim($row[5]);
                $csvKompetensi = $row[6] ?? null;

                if (empty($csvNama)) {
                    $this->processedRows++;
                    $file->next();
                    if ($file->eof()) {
                        break;
                    }
                    continue;
                }

                $jurusanEnum =
                    JurusanAsisten::tryFrom($csvJurusan) ??
                    JurusanAsisten::Teknik_Informatika;

                if ($csvNpm === "-" || empty($csvNpm)) {
                    $finalNpm = "TMP-" . substr(uniqid(), -6);
                    $password = "12345678";
                } else {
                    $finalNpm = $csvNpm;
                    $password = $csvNpm;
                }

                $statusAktif = filter_var($csvStatus, FILTER_VALIDATE_BOOLEAN);

                $asisten = Asisten::updateOrCreate(
                    ["email" => $csvEmail],
                    [
                        "nama" => $csvNama,
                        "npm" => $finalNpm,
                        "jurusan" => $jurusanEnum,
                        "no_telepon" => $csvNoHp,
                        "password" => bcrypt($password),
                        "status_aktif" => $statusAktif,
                        "tanggal_daftar" => now(),
                    ],
                );

                if (!empty($csvKompetensi)) {
                    $kompetensiIds = [];

                    foreach (explode("|", $csvKompetensi) as $namaKomp) {
                        $namaKomp = trim($namaKomp);
                        if ($namaKomp === "") {
                            continue;
                        }

                        $komp = Kompetensi::firstOrCreate(
                            ["nama_kompetensi" => $namaKomp],
                            ["deskripsi" => "Imported via CSV"],
                        );

                        $kompetensiIds[] = $komp->id_kompetensi;
                    }

                    $asisten
                        ->kompetensis()
                        ->syncWithoutDetaching($kompetensiIds);
                }

                $this->processedRows++;
                $this->successCount++;

                $file->next();
                $count++;

                if ($file->eof() && empty($file->current())) {
                    break;
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->isImporting = false;

            Notification::make()
                ->danger()
                ->title("Import gagal")
                ->body($e->getMessage())
                ->send();

            return;
        }

        if (
            $this->processedRows >= $this->totalRows ||
            ($file->eof() && empty($file->current()))
        ) {
            $this->isImporting = false;
            Storage::disk("local")->delete($this->currentFilePath);
            $this->form->fill();
            $this->processedRows = $this->totalRows;

            Notification::make()
                ->success()
                ->title("Import selesai")
                ->body("{$this->successCount} data berhasil diimport")
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_template')
                ->label('Download Template CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => 'attachment; filename="template_asisten.csv"',
                    ];

                    $columns = ['Nama Asisten', 'NPM', 'Jurusan', 'Email', 'NoHP', 'Status Aktif', 'Kompetensi'];
                    
                    $sample = ['Budi Santoso', '2022001', 'Teknik Informatika', 'budi@example.com', '08123456789', '1', 'Laravel|Flutter'];

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
