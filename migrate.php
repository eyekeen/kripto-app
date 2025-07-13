<?php
require_once __DIR__.'/vendor/autoload.php';

use App\Database\Migrator;

// Выполняем миграции
Migrator::migrate();
echo "Migrations completed successfully!\n";