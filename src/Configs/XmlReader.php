<?php

namespace Betalabs\Engine\Configs;

class XmlReader
{

    /**
     * Read the given XML filename
     *
     * @param string $filename
     * @return \SimpleXMLElement|false false when the file is missing or invalid
     */
    public function load($filename)
    {
        return simplexml_load_file($filename);
    }

}