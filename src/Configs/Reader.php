<?php

namespace Betalabs\Engine\Configs;

use Betalabs\Engine\Configs\Exceptions\ConfigDoesNotExistException;

class Reader
{

    /** @var \Betalabs\Engine\Configs\XmlReader */
    protected $xmlReader;

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
        $this->rootPath = rtrim($rootPath, '/') .'/';
    }

    /**
     * Reader constructor.
     * @param \Betalabs\Engine\Configs\XmlReader $xmlReader
     */
    public function __construct(XmlReader $xmlReader)
    {
        $this->xmlReader = $xmlReader;
        $this->rootPath = __DIR__ .'/../../../../../';
    }

    /**
     * Load all configurations from XML
     *
     * @return \SimpleXMLElement
     * @throws \Betalabs\Engine\Configs\Exceptions\ConfigDoesNotExistException
     */
    public function load()
    {

        if(!is_null(self::$configObject)) {
            return self::$configObject;
        }

        self::$configObject = $this->xmlReader->load($this->rootPath .'engine-sdk.xml');

        if(self::$configObject === false) {
            throw new ConfigDoesNotExistException('Config file not found in '. $this->rootPath .'engine-sdk.xml');
        }

        return self::$configObject;

    }

    /**
     * Clear all configs
     *
     * Can be used to make a new load with fresh data
     */
    public function clearConfigs()
    {
        self::$configObject = null;
    }

}