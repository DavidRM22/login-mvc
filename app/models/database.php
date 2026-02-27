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
            } catch (PDOException $e) {
                throw new RuntimeException("Error de conexión a MySQL: " . $e->getMessage(), 0, $e);
            }
        }

        return self::$connection;
    }
}
