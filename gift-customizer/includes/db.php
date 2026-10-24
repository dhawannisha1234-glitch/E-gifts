<?php
/**
 * Database Connection & Abstraction Layer
 * Supports MySQL PDO with graceful JSON fallback for portable zero-setup operation.
 */

define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'gift_customizer_db');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

class Database {
    private static ?PDO $pdo = null;
    private static bool $connectionAttempted = false;
    private static bool $isConnected = false;

    public static function getConnection(): ?PDO {
        if (self::$connectionAttempted) {
            return self::$pdo;
        }

        self::$connectionAttempted = true;
        
        // Only attempt connection if PDO MySQL is available
        if (!extension_loaded('pdo_mysql')) {
            return null;
        }

        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_SILENT,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT            => 2, // short timeout so it doesn't block if MySQL is offline
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            self::$pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            self::$isConnected = true;
            return self::$pdo;
        } catch (PDOException $e) {
            // MySQL is not currently running or database doesn't exist yet;
            // The system gracefully falls back to JSON persistence.
            self::$pdo = null;
            self::$isConnected = false;
            return null;
        }
    }

    public static function isConnected(): bool {
        self::getConnection();
        return self::$isConnected;
    }
}
