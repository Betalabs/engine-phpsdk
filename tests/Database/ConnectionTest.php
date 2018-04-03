<?php

namespace Betalabs\Engine\Tests\Database;

use Betalabs\Engine\Database\Connection;
use Betalabs\Engine\Tests\TestCase;

class ConnectionTest extends TestCase
{

    public function testGet()
    {
        $this->assertInstanceOf(\PDO::class, Connection::get());
    }
}
