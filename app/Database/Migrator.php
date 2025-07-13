<?php
namespace App\Database;


use App\Database\Migrations\CreateCryptocurrenciesTable;

class Migrator
{
    public static function migrate()
    {
        $migrations = [
            new CreateCryptocurrenciesTable()
        ];
        
        foreach ($migrations as $migration) {
            $migration->up();
        }
    }
    
    public static function rollback()
    {
        $migrations = [
            new CreateCryptocurrenciesTable()
        ];
        
        foreach ($migrations as $migration) {
            $migration->down();
        }
    }
}