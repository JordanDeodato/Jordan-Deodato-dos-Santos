<?php

namespace App\Jobs;

use App\Models\ImportedData;
use App\Models\ImportedDataCsv;
use App\Notifications\ImportCompletedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessCsvImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $filePath,
        public $user = null
    ) {}

    public function handle()
    {
        $importedCount = 0;
        $errors = 0;

        if (!file_exists($this->filePath)) {
            Log::error("Arquivo CSV não encontrado: {$this->filePath}");
            return;
        }

        $file = fopen($this->filePath, 'r');
        fgetcsv($file); 

        while (($data = fgetcsv($file)) !== false) {
            try {
                ImportedDataCsv::create([
                    'data' => $data[0],
                    'temperatura' => (float) str_replace(',', '.', $data[1])
                ]);
                $importedCount++;
            } catch (\Exception $e) {
                $errors++;
                Log::error("Erro na linha: " . implode(',', $data) . " - " . $e->getMessage());
            }
        }

        fclose($file);

        if ($this->user) {
            $this->user->notify(new ImportCompletedNotification(
                basename($this->filePath),
                $importedCount,
                $errors
            ));
        }

        Log::info("Importação concluída. Sucessos: {$importedCount}, Erros: {$errors}");
    }
}