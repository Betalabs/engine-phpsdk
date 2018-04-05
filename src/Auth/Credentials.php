<?php


namespace Betalabs\Engine\Auth;


class Credentials
{
    /**
     * @var string
     */
    public static $identifier;
    /**
     * @var string
     */
    public static $apiUri;
    /**
     * @var string
     */
    public static $username;
    /**
     * @var string
     */
    public static $password;
    /**
     * @var string
     */
    public static $id;
    /**
     * @var string
     */
    public static $secret;

    /**
     * Check if credentials is filled
     *
     * @return bool
     */
    public static function isValid(): bool
    {
        return self::$identifier !== null
            && self::$apiUri !== null
            && self::$username !== null
            && self::$password !== null
            && self::$id !== null
            && self::$secret !== null;
    }

    /**
     * Clear credentials from memory
     */
    public static function clear(): void
    {
        self::$identifier = null;
        self::$apiUri = null;
        self::$username = null;
        self::$password = null;
        self::$id = null;
        self::$secret = null;
    }

    /**
     * Return object copy of Credentials class
     *
     * @throws \ReflectionException
     */
    public static function retrieve()
    {
        $reflection = new \ReflectionClass(self::class);
        return (object)$reflection->getStaticProperties();
    }
}