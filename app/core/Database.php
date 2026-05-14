<?php

declare(strict_types=1);

class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $config = require __DIR__ . '/../config/config.php';

            if (isset($config['database']['path'])) {
                $dbPath = $config['database']['path'];
            } else {
                $dbPath = null;
            }

            if (!$dbPath) {
                throw new RuntimeException('Calea catre baza de date nu este definita in configurare.');
            }

            if (!file_exists($dbPath)) {
                throw new RuntimeException("Fisierul bazei de date nu exista: {$dbPath}");
            }

            $pdo = new PDO('sqlite:' . $dbPath);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->exec('PRAGMA foreign_keys = ON');

            self::$connection = $pdo;
        }

        return self::$connection;
    }
}