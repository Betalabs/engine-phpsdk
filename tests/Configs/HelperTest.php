<?php

namespace Betalabs\Engine\Tests\Configs;

use Betalabs\Engine\Configs\Helper;
use Betalabs\Engine\Tests\Fixtures\FakeRouteProvider;
use Betalabs\Engine\Tests\TestCase;

class HelperTest extends TestCase
{

    public function testFileExists()
    {

        $helper = new Helper();

        $this->assertTrue($helper->fileExists($this->fixture('engine-sdk-valid.xml')));
        $this->assertFalse($helper->fileExists($this->fixture('does-not-exist.xml')));

    }

    public function testClassExists()
    {

        $helper = new Helper();

        $this->assertTrue($helper->classExists(FakeRouteProvider::class));
        $this->assertFalse($helper->classExists('Betalabs\Engine\Tests\Fixtures\DoesNotExist'));

    }

    public function testRequireFileOnceLoadsClassFile()
    {

        $helper = new Helper();
        $class = 'BetalabsEngineTestsRequired\HelperRequiredClass';

        $this->assertFalse(class_exists($class, false));

        $helper->requireFileOnce($this->fixture('HelperRequiredClass.php'));
        // Second call must not fail redeclaring the class
        $helper->requireFileOnce($this->fixture('HelperRequiredClass.php'));

        $this->assertTrue(class_exists($class, false));

    }

    protected function fixture($file)
    {
        return __DIR__ . '/../Fixtures/files/' . $file;
    }

}
