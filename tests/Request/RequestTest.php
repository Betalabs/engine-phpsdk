<?php

namespace Betalabs\Engine\Tests\Request;

use Betalabs\Engine\Requests\Methods\Delete;
use Betalabs\Engine\Requests\Methods\Get;
use Betalabs\Engine\Requests\Methods\Patch;
use Betalabs\Engine\Requests\Methods\Post;
use Betalabs\Engine\Requests\Methods\Put;
use Betalabs\Engine\Requests\Request;
use Betalabs\Engine\Tests\TestCase;

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