<?php

namespace Betalabs\Engine\Configs;

class XmlReader
{

    /**
     * Read the given XML filename
     *
     * @param string $filename
     * @return \SimpleXMLElement
     */
    public function load($filename)
    {
        return simplexml_load_file($filename);
    }

}