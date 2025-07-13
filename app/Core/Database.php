<?php
namespace App\Core;

class Database
{
    public const DB_PATH = __DIR__ . '/../../storage/database/';
    public const DB_FILE = 'app.db';
    
    public static function getConnection(): \PDO
    {
        $dsn = 'sqlite:' . self::DB_PATH . self::DB_FILE;
        
        try {
            $pdo = new \PDO($dsn);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
}