<?php
namespace App\Console;

use App\Services\CryptoApiService;

class UpdateCryptoDataCommand
{
    public function execute($show_message = true)
    {
        $apiService = new CryptoApiService();
        $result = $apiService->fetchTopCryptocurrencies(50);
        if($show_message){
            echo "Successfully updated " . count($result) . " cryptocurrencies.\n";
        }
    }
}