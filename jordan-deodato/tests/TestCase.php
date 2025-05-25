<?php

namespace Tests;

use Exception;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        $dbName = config('database.connections.mysql.database');
        config(['database.connections.mysql.database' => null]);

        try {
            DB::statement("CREATE DATABASE IF NOT EXISTS `$dbName`");
        } catch (Exception $e) {
            dump("Erro criando banco de teste: " . $e->getMessage());
        }

        config(['database.connections.mysql.database' => $dbName]);
    }
}
