<?php
namespace App\Database\Migrations;

use App\Core\Database;

class CreateCryptocurrenciesTable
{
    public function up()
    {
        $pdo = Database::getConnection();
        
        $sql = "CREATE TABLE IF NOT EXISTS cryptocurrencies (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            ticker TEXT NOT NULL UNIQUE,
            price REAL NOT NULL,
            change_24h REAL NOT NULL,
            market_cap REAL NOT NULL,
            trading_volume REAL NOT NULL,
            chart_data TEXT,
            last_updated DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        
        $pdo->exec($sql);
        
        // Создадим индекс для быстрого поиска по тикеру
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_ticker ON cryptocurrencies (ticker)");
    }
    
    public function down()
    {
        $pdo = Database::getConnection();
        $pdo->exec("DROP TABLE IF EXISTS cryptocurrencies");
    }
}