<?php

class Database
{
    private static $connection = null;

    public static function connect()
    {
        if (self::$connection === null) {
            try {
                self::$connection = new PDO(
                    "mysql:host=localhost;dbname=auditoria;charset=utf8",
                    "root",
                    ""
                );
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::ensureSchema();
            } catch (PDOException $e) {
                die("Error de conexión: " . $e->getMessage());
            }
        }

        return self::$connection;
    }

    private static function ensureSchema()
    {
        $queries = [
            "CREATE TABLE IF NOT EXISTS users (
                id VARCHAR(50) PRIMARY KEY,
                name VARCHAR(120) NOT NULL,
                email VARCHAR(191) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                created_at DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS otp_codes (
                email VARCHAR(191) PRIMARY KEY,
                code VARCHAR(6) NOT NULL,
                expires_at DATETIME NOT NULL,
                created_at DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
            "CREATE TABLE IF NOT EXISTS audit_logs (
                id BIGINT AUTO_INCREMENT PRIMARY KEY,
                event VARCHAR(120) NOT NULL,
                email VARCHAR(191) NOT NULL,
                ip VARCHAR(45) NOT NULL,
                user_agent TEXT NOT NULL,
                created_at DATETIME NOT NULL,
                details TEXT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        ];

        foreach ($queries as $sql) {
            self::$connection->exec($sql);
        }
        $migrationQueries = [
            "ALTER TABLE users MODIFY COLUMN id VARCHAR(50) NOT NULL",
            "ALTER TABLE users MODIFY COLUMN name VARCHAR(120) NOT NULL",
            "ALTER TABLE users MODIFY COLUMN email VARCHAR(191) NOT NULL",
            "ALTER TABLE users MODIFY COLUMN password VARCHAR(255) NOT NULL",
            "ALTER TABLE users MODIFY COLUMN created_at DATETIME NOT NULL",
            "ALTER TABLE otp_codes MODIFY COLUMN email VARCHAR(191) NOT NULL",
            "ALTER TABLE otp_codes MODIFY COLUMN code VARCHAR(6) NOT NULL",
            "ALTER TABLE otp_codes MODIFY COLUMN expires_at DATETIME NOT NULL",
            "ALTER TABLE otp_codes MODIFY COLUMN created_at DATETIME NOT NULL",
            "ALTER TABLE audit_logs MODIFY COLUMN event VARCHAR(120) NOT NULL",
            "ALTER TABLE audit_logs MODIFY COLUMN email VARCHAR(191) NOT NULL",
            "ALTER TABLE audit_logs MODIFY COLUMN ip VARCHAR(45) NOT NULL",
            "ALTER TABLE audit_logs MODIFY COLUMN created_at DATETIME NOT NULL"
        ];

        foreach ($migrationQueries as $sql) {
            self::$connection->exec($sql);
        }
    }
}
