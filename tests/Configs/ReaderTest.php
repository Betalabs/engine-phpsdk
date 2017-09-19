<?php

namespace Betalabs\Engine\Tests\Configs;

use Betalabs\Engine\Configs\Exceptions\ConfigDoesNotExistException;
use Betalabs\Engine\Configs\Reader;
use Betalabs\Engine\Configs\XmlReader;
use PHPUnit\Framework\TestCase;

class ReaderTest extends TestCase
{

    public function testLoadExistingFile()
    {

        $simpleXMLElement = new \SimpleXMLElement("<xml></xml>");

        $xmlReader = \Mockery::mock(XmlReader::class);
        $xmlReader->shouldReceive('load')
            ->with('/path/to/root/engine-sdk.xml')
            ->andReturn($simpleXMLElement);

        $reader = new Reader($xmlReader);

        $reader->setRootPath('/path/to/root/');

        $this->assertEquals(
            $simpleXMLElement,
            $reader->load()
        );

    }

    public function testLoadUnexistingFile()
    {

        $this->expectException(ConfigDoesNotExistException::class);

        $xmlReader = \Mockery::mock(XmlReader::class);
        $xmlReader->shouldReceive('load')
            ->with('/path/to/root/engine-sdk.xml')
            ->andReturn(false);

        $reader = new Reader($xmlReader);

        $reader->setRootPath('/path/to/root/');

        $reader->load();

    }

}