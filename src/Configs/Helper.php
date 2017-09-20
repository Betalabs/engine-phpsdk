<?php

namespace Betalabs\Engine\Configs;

class Helper
{

    /**
     * Check if file exists
     *
     * @param string $filename
     * @return bool
     */
    public function fileExists($filename)
    {
        return file_exists($filename);
    }

    /**
     * Check if class exists
     *
     * @param string $className
     * @return bool
     */
    public function classExists($className)
    {
        return class_exists($className);
    }

    /**
     * require_once helper
     *
     * @param string $path
     */
    public function requireFileOnce($path)
    {
        require_once $path;
    }

}