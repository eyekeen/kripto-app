<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\CryptoApiService;

class CryptocurrencyController extends Controller
{
    private $apiService;
    
    public function __construct()
    {
        $this->apiService = new CryptoApiService();
    }
    
    public function index()
    {
        $cryptocurrencies = $this->apiService->fetchTopCryptocurrencies(50);
        $this->render('cryptocurrencies/index', [
            'cryptocurrencies' => $cryptocurrencies,
            'title' => 'Top 50 Cryptocurrencies by Market Cap'
        ]);
    }
    
    public function show(string $ticker)
    {
        $cryptoModel = new \App\Models\Cryptocurrency();
        $crypto = $cryptoModel->getByTicker($ticker);
        
        if (!$crypto) {
            $this->render('errors/404', [], 404);
            return;
        }
        
        $this->render('cryptocurrencies/show', [
            'crypto' => $crypto,
            'title' => $crypto['name'] . ' (' . $crypto['ticker'] . ') Details'
        ]);
    }
    
    public function apiIndex()
    {
        $cryptocurrencies = $this->apiService->fetchTopCryptocurrencies(50);
        $this->json($cryptocurrencies);
    }
}