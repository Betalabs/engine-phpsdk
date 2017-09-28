<?php

namespace Betalabs\Engine\Tests\Database;

use Betalabs\Engine\Database\BootResponse;
use Betalabs\Engine\Tests\TestCase;

class BootResponseTest extends TestCase
{

    public function testBothDataInformed()
    {

        $bootResponse = new BootResponse(
            true,
            'Response message'
        );

        $this->assertEquals(
            true,
            $bootResponse->isSuccess()
        );

        $this->assertEquals(
            'Response message',
            $bootResponse->getMessage()
        );

        $this->assertEquals(
            [
                'success' => true,
                'message' => 'Response message'
            ],
            $bootResponse->formatResponse()
        );

    }

    public function testOnlySuccessInformed()
    {

        $bootResponse = new BootResponse(
            false
        );

        $this->assertEquals(
            false,
            $bootResponse->isSuccess()
        );

        $this->assertNull($bootResponse->getMessage());

        $this->assertEquals(
            ['success' => false],
            $bootResponse->formatResponse()
        );

    }

}