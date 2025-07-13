<?php
require_once __DIR__.'/vendor/autoload.php';

use App\Console\UpdateCryptoDataCommand;

$command = new UpdateCryptoDataCommand();
$command->execute();