<?php
namespace App\Console;

use App\Services\CryptoApiService;

class UpdateCryptoDataCommand
{
    public function execute()
    {
        $apiService = new CryptoApiService();
        $result = $apiService->fetchTopCryptocurrencies(50);
        
        echo "Successfully updated " . count($result) . " cryptocurrencies.\n";
    }
}