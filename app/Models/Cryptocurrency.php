<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

class Cryptocurrency
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    // Добавление новой криптовалюты
    public function create(array $data): bool
    {
        $sql = "INSERT INTO cryptocurrencies 
                (name, ticker, price, change_24h, market_cap, trading_volume, chart_data) 
                VALUES (:name, :ticker, :price, :change_24h, :market_cap, :trading_volume, :chart_data)";

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':name' => $data['name'],
                ':ticker' => $data['ticker'],
                ':price' => $data['price'],
                ':change_24h' => $data['change_24h'],
                ':market_cap' => $data['market_cap'],
                ':trading_volume' => $data['trading_volume'],
                ':chart_data' => $data['chart_data'] ?? null
            ]);
        } catch (PDOException $e) {
            // Логирование ошибки
            error_log($e->getMessage());
            return false;
        }
    }

    // Обновление данных криптовалюты
    public function update(string $ticker, array $data): bool
    {
        $sql = "UPDATE cryptocurrencies SET 
                name = :name,
                price = :price,
                change_24h = :change_24h,
                market_cap = :market_cap,
                trading_volume = :trading_volume,
                chart_data = :chart_data,
                last_updated = CURRENT_TIMESTAMP
                WHERE ticker = :ticker";

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':ticker' => $ticker,
                ':name' => $data['name'],
                ':price' => $data['price'],
                ':change_24h' => $data['change_24h'],
                ':market_cap' => $data['market_cap'],
                ':trading_volume' => $data['trading_volume'],
                ':chart_data' => $data['chart_data'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    // Получение всех криптовалют, отсортированных по капитализации
    public function getAllByMarketCap(int $limit = 50): array
    {
        $sql = "SELECT * FROM cryptocurrencies 
                ORDER BY market_cap DESC 
                LIMIT :limit";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // Получение криптовалюты по тикеру
    public function getByTicker(string $ticker): ?array
    {
        $sql = "SELECT * FROM cryptocurrencies WHERE ticker = :ticker";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':ticker' => $ticker]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    // Удаление криптовалюты
    public function delete(string $ticker): bool
    {
        $sql = "DELETE FROM cryptocurrencies WHERE ticker = :ticker";

        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([':ticker' => $ticker]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function getPaginated(int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT * FROM cryptocurrencies 
            ORDER BY market_cap DESC 
            LIMIT :limit OFFSET :offset";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function getTotalCount(): int
    {
        try {
            $stmt = $this->pdo->query("SELECT COUNT(*) FROM cryptocurrencies");
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return 0;
        }
    }

    public function search(string $query, int $limit = 50): array
    {
        $sql = "SELECT * FROM cryptocurrencies 
            WHERE name LIKE :query OR ticker LIKE :query
            ORDER BY market_cap DESC 
            LIMIT :limit";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':query', '%' . $query . '%');
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function filterByMarketCap(float $minCap, float $maxCap): array
    {
        $sql = "SELECT * FROM cryptocurrencies 
            WHERE market_cap BETWEEN :minCap AND :maxCap
            ORDER BY market_cap DESC";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':minCap' => $minCap,
                ':maxCap' => $maxCap
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }
}
