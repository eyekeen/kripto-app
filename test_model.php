<?php
require_once __DIR__.'/vendor/autoload.php';

use App\Models\Cryptocurrency;

// Создаем экземпляр модели
$cryptoModel = new Cryptocurrency();

// Тестовые данные
$testData = [
    'name' => 'Bitcoin',
    'ticker' => 'BTC',
    'price' => 50000.00,
    'change_24h' => 2.5,
    'market_cap' => 950000000000,
    'trading_volume' => 25000000000,
    'chart_data' => json_encode(['labels' => ['1d', '1w', '1m'], 'data' => [48000, 49000, 50000]])
];

// 1. Создание записи
if ($cryptoModel->create($testData)) {
    echo "Cryptocurrency created successfully!\n";
} else {
    echo "Failed to create cryptocurrency.\n";
}

// 2. Получение всех криптовалют
$allCrypto = $cryptoModel->getAllByMarketCap();
echo "Top cryptocurrencies:\n";
print_r($allCrypto);

// 3. Получение по тикеру
$btc = $cryptoModel->getByTicker('BTC');
echo "Bitcoin data:\n";
print_r($btc);

// 4. Обновление данных
$updateData = [
    'name' => 'Bitcoin',
    'price' => 51000.00,
    'change_24h' => 3.2,
    'market_cap' => 960000000000,
    'trading_volume' => 26000000000
];

if ($cryptoModel->update('BTC', $updateData)) {
    echo "Cryptocurrency updated successfully!\n";
} else {
    echo "Failed to update cryptocurrency.\n";
}

// Проверяем обновленные данные
$updatedBtc = $cryptoModel->getByTicker('BTC');
echo "Updated Bitcoin data:\n";
print_r($updatedBtc);