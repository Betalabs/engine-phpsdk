<?php

namespace Betalabs\Engine\Tests;

class TestCase extends \PHPUnit\Framework\TestCase
{

    public function tearDown(): void {
        \Mockery::close();
    }

}
