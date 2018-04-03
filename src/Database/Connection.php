<?php


namespace Betalabs\Engine\Database;


class Connection
{
    private static $conn;

    public static function get()
    {
        if (self::$conn === null) {
            self::$conn = new \PDO('sqlite:database.sqlite3');
        }

        return self::$conn;
    }
}