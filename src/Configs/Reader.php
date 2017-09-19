<?php

namespace Betalabs\Engine\Configs;

class Reader
{

    /** @var string */
    protected $rootPath;

    /** @var \SimpleXMLElement */
    protected static $configObject;

    /**
     * @return string
     */
    public function getRootPath(): string
    {
        return $this->rootPath;
    }

    /**
     * @param string $rootPath
     */
    public function setRootPath(string $rootPath)
    {
        $this->rootPath = $rootPath;
    }

    /**
     * Reader constructor.
     */
    public function __construct()
    {
        $this->rootPath = __DIR__ .'/../../../../';
    }

    /**
     * Load all configurations from XML
     *
     * @return \SimpleXMLElement
     */
    public function load()
    {

        if(is_null(self::$configObject)) {
            self::$configObject = simplexml_load_file($this->rootPath .'engine-sdk.xml');
        }

        return self::$configObject;

    }

}