<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\CryptoApiService;
use App\Console\UpdateCryptoDataCommand;


class CryptocurrencyController extends Controller
{
    private $apiService;

    public function __construct()
    {
        $this->apiService = new CryptoApiService();
    }

    public function index()
    {
        
        $command = new UpdateCryptoDataCommand();
        $command->execute(false);

        $searchQuery = $_GET['search'] ?? '';

        if (!empty($searchQuery)) {
            $cryptocurrencies = $this->apiService->searchData($searchQuery);
            $this->render('cryptocurrencies/index', [
                'cryptocurrencies' => $cryptocurrencies,
                'title' => 'Search Results for: ' . $searchQuery,
                'searchQuery' => $searchQuery
            ]);
        } else {
            $page = max(1, (int)($_GET['page'] ?? 1));
            $perPage = 20;
            $cryptocurrencies = $this->apiService->getPaginatedData($page, $perPage);
            $total = $this->apiService->getTotalCount();

            $this->render('cryptocurrencies/index', [
                'cryptocurrencies' => $cryptocurrencies,
                'title' => 'Top Cryptocurrencies by Market Cap',
                'pagination' => [
                    'page' => $page,
                    'perPage' => $perPage,
                    'total' => $total,
                    'totalPages' => ceil($total / $perPage)
                ],
                'searchQuery' => $searchQuery
            ]);
        }
    }
}
