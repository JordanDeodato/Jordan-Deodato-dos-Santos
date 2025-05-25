<?php

namespace App\Console\Commands;

use App\Jobs\ProcessCsvImport;
use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ImportCsvFile extends Command
{
    protected $signature = 'import:csv {file?}';
    protected $description = 'Importa dados de um arquivo CSV para o banco de dados';

    public function handle()
    {
        $file = $this->argument('file') ?? public_path('assets/files/example.csv');

        if (!file_exists($file)) {
            $this->error("Arquivo não encontrado: $file");
            return;
        }

        $user = Auth::user();

        ProcessCsvImport::dispatch($file, $user)
            ->onQueue('imports');

        $this->info("⏳ Importação do arquivo $file iniciada em segundo plano...");
        $this->info("💡 Execute 'php artisan queue:work --queue=imports' para processar a importação.");
    }
}