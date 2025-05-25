<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class SetupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria o banco de dados (local ou testing) se não existir, roda migrations e seeders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Criando banco de dados principal...");
        $this->createDatabaseFromEnv('DB_DATABASE');

        $this->info("Executando migrations no banco principal...");
        $this->runMigrations(env('DB_DATABASE'));

        $this->info("Executando seeders no banco principal...");
        $this->runSeeders(env('DB_DATABASE'));

        $this->info("Criando banco de dados de testes...");
        $this->createDatabaseFromEnv('DB_DATABASE_TEST');

        $this->info("Executando migrations no banco de testes...");
        $this->runMigrations(env('DB_DATABASE_TEST'), true);
    }

    protected function createDatabaseFromEnv(string $envKey)
    {
        $host = env('DB_HOST', '127.0.0.1');
        $port = env('DB_PORT', 3306);
        $username = env('DB_USERNAME', 'root');
        $password = env('DB_PASSWORD', '');
        $database = env($envKey);

        try {
            $pdo = new \PDO("mysql:host=$host;port=$port", $username, $password);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $this->info("Banco de dados `$database` criado ou já existente.");
        } catch (\PDOException $e) {
            $this->error("Erro ao criar o banco `$database`: " . $e->getMessage());
        }
    }

    protected function runMigrations(string $databaseName, bool $isTesting = false)
    {
        $connection = config('database.default');

        Config::set("database.connections.$connection.database", $databaseName);
        DB::purge($connection);
        DB::reconnect($connection);

        $this->call('migrate', ['--database' => $connection]);
    }

    protected function runSeeders(string $databaseName)
    {
        $connection = config('database.default');

        Config::set("database.connections.$connection.database", $databaseName);
        DB::purge($connection);
        DB::reconnect($connection);

        $this->call('db:seed');
    }
}
