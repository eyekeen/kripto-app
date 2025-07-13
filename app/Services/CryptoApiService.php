<?php
namespace App\Services;

use App\Core\Database;
use App\Models\Cryptocurrency;

class CryptoApiService
{
    private const API_URL = 'https://api.coingecko.com/api/v3/';
    private const CACHE_TIME = 300; // 5 минут в секундах
    
    private $cryptoModel;
    
    public function __construct()
    {
        $this->cryptoModel = new Cryptocurrency();
    }
    
    public function fetchTopCryptocurrencies(int $limit = 50): array
    {
        // Проверяем, есть ли актуальные данные в базе
        $lastUpdated = $this->getLastUpdateTime();
        
        if ($lastUpdated && (time() - strtotime($lastUpdated) < self::CACHE_TIME)) {
            return $this->cryptoModel->getAllByMarketCap($limit);
        }
        
        // Если данные устарели, получаем новые с API
        try {
            $data = $this->makeApiRequest('coins/markets', [
                'vs_currency' => 'usd',
                'order' => 'market_cap_desc',
                'per_page' => $limit,
                'page' => 1,
                'sparkline' => true
            ]);
            
            $this->updateDatabase($data);
            
            return $this->cryptoModel->getAllByMarketCap($limit);
        } catch (\Exception $e) {
            // В случае ошибки API возвращаем данные из базы
            error_log('API Error: ' . $e->getMessage());
            return $this->cryptoModel->getAllByMarketCap($limit);
        }
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
    
    private function getLastUpdateTime(): ?string
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("SELECT MAX(last_updated) as last_update FROM cryptocurrencies");
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $result['last_update'] ?? null;
    }
}