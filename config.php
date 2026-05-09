<?php

class Database
{
    private static string $host = 'localhost';
    private static string $dbname = 'medicare';
    private static string $username = 'root';
    private static string $password = '';
    private static ?PDO $conn = null;

    public static function connect(): PDO
    {
        if (self::$conn === null) {
            self::$conn = new PDO(
                'mysql:host=' . self::$host . ';dbname=' . self::$dbname . ';charset=utf8mb4',
                self::$username,
                self::$password
            );

            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        return self::$conn;
    }
}