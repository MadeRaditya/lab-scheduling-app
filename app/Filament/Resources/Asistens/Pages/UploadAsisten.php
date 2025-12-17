<?php

namespace App\Filament\Resources\Asistens\Pages;

use App\Enums\JurusanAsisten;
use App\Filament\Resources\Asistens\AsistenResource;
use App\Models\Asisten;
use App\Models\Kompetensi;
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

    public function getView(): string
    {
        return 'filament.resources.asistens.pages.upload-asisten';
    }
    protected static string $resource = AsistenResource::class;
    protected static ?string $title = 'Upload Data Asisten';

    public ?array $data = [];
    
    public bool $isImporting = false;
    public int $totalRows = 0;
    public int $processedRows = 0;
    public string $currentFilePath = '';
    public int $successCount = 0;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FileUpload::make('csv_file')
                    ->label("Upload CSV Asisten")
                    ->helperText('Format: Nama;NPM;Jurusan;Email;NoHP;List_Kompetensi(koma)')
                    ->acceptedFileTypes(['text/csv', 'application/csv', 'text/plain'])
                    ->directory('csv/asisten')
                    ->disk('local')
                    ->required(),
            ])
            ->statePath('data');
    }

    public function startImport()
    {
        $data = $this->form->getState();
        if (empty($data['csv_file'])) return;

        $filePath = $data['csv_file'];
        $fullPath = Storage::disk('local')->path($filePath);

        if (!file_exists($fullPath)) {
            Notification::make()->danger()->title('File tidak ditemukan!')->send();
            return;
        }

        $file = new \SplFileObject($fullPath, 'r');
        $file->seek(PHP_INT_MAX);
        $this->totalRows = $file->key();
        
        $this->currentFilePath = $filePath;
        $this->processedRows = 0;
        $this->successCount = 0;
        $this->isImporting = true;
    }

    public function importBatch()
    {
        if (!$this->isImporting) return;

        $fullPath = Storage::disk('local')->path($this->currentFilePath);
        $file = new \SplFileObject($fullPath, 'r');
        $file->setFlags(\SplFileObject::READ_CSV);
        $file->setCsvControl(';'); 
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
                
                if (empty($row) || count($row) < 4) {
                    $file->next();
                    $this->processedRows++;
                    continue; 
                }

                $csvNama        = trim($row[0]);
                $csvNpm         = trim($row[1]);
                $csvJurusan     = trim($row[2]);
                $csvEmail       = trim($row[3]);
                $csvNoHp        = isset($row[4]) ? trim($row[4]) : null;
                $csvKompetensi  = isset($row[5]) ? trim($row[5]) : null;

                $jurusanEnum = JurusanAsisten::tryFrom($csvJurusan);
                if (!$jurusanEnum) {
                     $jurusanEnum = JurusanAsisten::Teknik_Informatika; 
                }

                // 2. Create / Update Data Asisten
                $asisten = Asisten::updateOrCreate(
                    ['npm' => $csvNpm], 
                    [
                        'nama' => $csvNama,
                        'email' => $csvEmail,
                        'jurusan' => $jurusanEnum,
                        'no_telepon' => $csvNoHp,
                        'password' => bcrypt($csvNpm), // Default password = NPM
                        'status_aktif' => true,
                        'tanggal_daftar' => now(),
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
                            ['deskripsi' => 'Imported via CSV']
                        );
                        $kompetensiIds[] = $komp->id_kompetensi;
                    }
                    
                    $asisten->kompetensis()->syncWithoutDetaching($kompetensiIds);
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
            Storage::disk('local')->delete($this->currentFilePath);
            $this->form->fill();
            
            Notification::make()->success()
                ->title('Import Selesai')
                ->body("{$this->successCount} asisten berhasil diproses.")
                ->send();
        }
    }
}