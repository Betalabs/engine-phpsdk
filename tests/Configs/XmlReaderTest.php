<?php

namespace Betalabs\Engine\Tests\Configs;

use Betalabs\Engine\Configs\XmlReader;
use Betalabs\Engine\Tests\TestCase;

class XmlReaderTest extends TestCase
{

    public function testLoadValidFileReturnsXmlElement()
    {

        $xmlReader = new XmlReader();

        $config = $xmlReader->load($this->fixture('engine-sdk-valid.xml'));

        $this->assertInstanceOf(\SimpleXMLElement::class, $config);
        $this->assertEquals('Sandbox', (string) $config->environment->env);
        $this->assertEquals(
            'Betalabs\Engine\Tests\Fixtures\FakeRouteProvider',
            (string) $config->routeProvider->class
        );

    }

    public function testLoadMissingFileReturnsFalse()
    {

        $xmlReader = new XmlReader();

        // simplexml_load_file() also emits a warning; Reader::load() relies on
        // the false return to throw ConfigDoesNotExistException
        $this->assertFalse(
            @$xmlReader->load($this->fixture('does-not-exist.xml'))
        );

    }

    public function testLoadMalformedFileReturnsFalse()
    {

        $xmlReader = new XmlReader();

        $this->assertFalse(
            @$xmlReader->load($this->fixture('engine-sdk-malformed.xml'))
        );

    }

    protected function fixture($file)
    {
        return __DIR__ . '/../Fixtures/files/' . $file;
    }

}
