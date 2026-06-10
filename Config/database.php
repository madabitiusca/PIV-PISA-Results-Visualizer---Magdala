<?php

class Database {
    private static ?PDO $connection = null;

    public static function getConnection(): PDO {
        if (self::$connection === null) {
            self::$connection = new PDO(
                'sqlite:' . __DIR__ . '/../Database/pisavis.sqlite'
            );

            self::$connection->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );
        }

        return self::$connection;
    }
}