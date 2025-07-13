<?php

namespace App\Services;

use App\Core\Database;
use App\Models\Cryptocurrency;

class CryptoApiService
{
    private const API_URL = 'https://api.coingecko.com/api/v3/';
    private const BACKUP_API_URL = 'https://api.coincap.io/v2/';
    private const CACHE_TIME = 300;

    private $cryptoModel;

    public function __construct()
    {
        $this->cryptoModel = new Cryptocurrency();
    }

    public function fetchTopCryptocurrencies(int $limit = 50): array
    {
        try {
            $data = $this->tryPrimaryApi($limit);
            $this->updateDatabase($data);
            return $this->cryptoModel->getAllByMarketCap($limit);
        } catch (\Exception $e) {
            error_log('API Error: ' . $e->getMessage());
            return $this->getCachedData($limit);
        }
    }

    private function tryPrimaryApi(int $limit): array
    {
        try {
            return $this->makeApiRequest('coins/markets', [
                'vs_currency' => 'usd',
                'order' => 'market_cap_desc',
                'per_page' => $limit,
                'page' => 1,
                'sparkline' => true
            ]);
        } catch (\Exception $e) {
            error_log('Primary API failed, trying backup: ' . $e->getMessage());
            return $this->tryBackupApi($limit);
        }
    }

    private function tryBackupApi(int $limit): array
    {
        try {
            $response = $this->makeApiRequest(self::BACKUP_API_URL . 'assets', [
                'limit' => $limit
            ], true);

            return array_map(function ($item) {
                return [
                    'name' => $item['name'],
                    'symbol' => $item['symbol'],
                    'current_price' => $item['priceUsd'],
                    'price_change_percentage_24h' => $item['changePercent24Hr'],
                    'market_cap' => $item['marketCapUsd'],
                    'total_volume' => $item['volumeUsd24Hr'],
                    'sparkline_in_7d' => ['price' => []]
                ];
            }, $response['data']);
        } catch (\Exception $e) {
            error_log('Backup API also failed: ' . $e->getMessage());
            throw new \Exception('All API sources unavailable');
        }
    }

    private function getCachedData(int $limit): array
    {
        $data = $this->cryptoModel->getAllByMarketCap($limit);

        if (empty($data)) {
            throw new \Exception('No cached data available');
        }

        return $data;
    }



    private function makeApiRequest(string $endpoint, array $params = []): array
    {
        $url = self::API_URL . $endpoint . '?' . http_build_query($params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        // Для работы с API CoinGecko может потребоваться User-Agent
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception('CURL Error: ' . curl_error($ch));
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpCode !== 200) {
            throw new \Exception('API Error: HTTP ' . $httpCode);
        }

        curl_close($ch);

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('JSON Error: ' . json_last_error_msg());
        }

        return $data;
    }

    private function updateDatabase(array $apiData): void
    {
        foreach ($apiData as $item) {
            $chartData = isset($item['sparkline_in_7d']['price']) ? json_encode([
                'labels' => range(1, count($item['sparkline_in_7d']['price'])),
                'data' => $item['sparkline_in_7d']['price']
            ]) : null;

            $cryptoData = [
                'name' => $item['name'],
                'ticker' => strtoupper($item['symbol']),
                'price' => $item['current_price'],
                'change_24h' => $item['price_change_percentage_24h'],
                'market_cap' => $item['market_cap'],
                'trading_volume' => $item['total_volume'],
                'chart_data' => $chartData
            ];

            // Проверяем, существует ли уже запись
            $existing = $this->cryptoModel->getByTicker($cryptoData['ticker']);

            if ($existing) {
                $this->cryptoModel->update($cryptoData['ticker'], $cryptoData);
            } else {
                $this->cryptoModel->create($cryptoData);
            }
        }
    }

    public function getPaginatedData(int $page = 1, int $perPage = 20): array
    {
        return $this->cryptoModel->getPaginated($page, $perPage);
    }

    public function getTotalCount(): int
    {
        return $this->cryptoModel->getTotalCount();
    }

    public function searchData(string $query): array
    {
        try {
            $data = $this->tryPrimaryApiSearch($query);
            $this->updateDatabase($data);
            return $this->cryptoModel->search($query);
        } catch (\Exception $e) {
            error_log('Search API Error: ' . $e->getMessage());
            return $this->cryptoModel->search($query);
        }
    }

    public function filterByMarketCap(float $minCap, float $maxCap): array
    {
        try {
            $data = $this->tryPrimaryApi(200); // Получаем больше записей для фильтрации
            $this->updateDatabase($data);
            return $this->cryptoModel->filterByMarketCap($minCap, $maxCap);
        } catch (\Exception $e) {
            error_log('Filter API Error: ' . $e->getMessage());
            return $this->cryptoModel->filterByMarketCap($minCap, $maxCap);
        }
    }

    private function tryPrimaryApiSearch(string $query): array
    {
        return $this->makeApiRequest('search', [
            'query' => $query
        ]);
    }
}
