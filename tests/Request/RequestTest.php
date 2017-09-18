<?php

namespace Betalabs\Engine\Tests\Request;

use Betalabs\Engine\Request\Methods\Delete;
use Betalabs\Engine\Request\Methods\Get;
use Betalabs\Engine\Request\Methods\Patch;
use Betalabs\Engine\Request\Methods\Post;
use Betalabs\Engine\Request\Methods\Put;
use Betalabs\Engine\Request\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{

    public function testGetReturnsAppropriateObject()
    {

        $request = new Request();

        $this->assertInstanceOf(
            Get::class,
            $request->get()
        );

    }

    public function testPostReturnsAppropriateObject()
    {

        $request = new Request();

        $this->assertInstanceOf(
            Post::class,
            $request->post()
        );

    }

    public function testPutReturnsAppropriateObject()
    {

        $request = new Request();

        $this->assertInstanceOf(
            Put::class,
            $request->put()
        );

    }

    public function testPatchReturnsAppropriateObject()
    {

        $request = new Request();

        $this->assertInstanceOf(
            Patch::class,
            $request->patch()
        );

    }

    public function testDeleteReturnsAppropriateObject()
    {

        $request = new Request();

        $this->assertInstanceOf(
            Delete::class,
            $request->delete()
        );

    }

}