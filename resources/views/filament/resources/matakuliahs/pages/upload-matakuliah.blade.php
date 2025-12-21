<x-filament-panels::page>
    <div 
        x-data="{ 
            isImporting: @entangle('isImporting'),
            progress: 0,
            total: @entangle('totalRows'),
            processed: @entangle('processedRows'),
            
            init() {
                $watch('isImporting', value => {
                    if (value) { this.processBatch(); }
                });
            },

            processBatch() {
                if (this.total > 0) {
                    this.progress = Math.round((this.processed / this.total) * 100);
                }

                $wire.importBatch().then(() => {
                    if (this.isImporting) {
                        setTimeout(() => this.processBatch(), 50);
                    } else {
                        this.progress = 100;
                    }
                });
            }
        }"
    >
        <form wire:submit="startImport" x-show="!isImporting">
            {{ $this->form }}

            <div class="mt-6">
                <x-filament::button type="submit" wire:target="startImport">
                    <span wire:loading.remove>Mulai Upload</span>
                    <span wire:loading>Memproses...</span>
                </x-filament::button>
            </div>
        </form>

        <div x-show="isImporting" class="mt-6 p-6 bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700" style="display: none;">
            <div class="mb-2 flex justify-between items-center">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-200">
                    Memproses Matakuliah... (<span x-text="processed"></span> / <span x-text="total"></span>)
                </span>
                <span class="text-sm font-bold text-primary-600 dark:text-primary-400" x-text="progress + '%'"></span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-4 dark:bg-gray-700 overflow-hidden">
                <div class="bg-primary-600 h-4 rounded-full transition-all duration-300 ease-out" :style="'width: ' + progress + '%'"></div>
            </div>
            <p class="mt-4 text-xs text-gray-500 dark:text-gray-400 text-center">Mohon jangan tutup halaman ini.</p>
        </div>
    </div>
</x-filament-panels::page>