<?php

namespace Betalabs\Engine\Tests;

use Betalabs\Engine\Requests\Methods\Delete;
use Betalabs\Engine\Requests\Methods\Get;
use Betalabs\Engine\Requests\Methods\Patch;
use Betalabs\Engine\Requests\Methods\Post;
use Betalabs\Engine\Requests\Methods\Put;
use Betalabs\Engine\Tests\TestCase;
use Betalabs\Engine\Request;

class RequestTest extends TestCase
{

    public function testGetMethodReturnAppropriateInstance()
    {

        $request = new Request();
        $this->assertInstanceOf(
            Get::class,
            $request->get()
        );

        $this->assertInstanceOf(
            Get::class,
            Request::get()
        );

    }

    public function testPostMethodReturnAppropriateInstance()
    {

        $request = new Request();
        $this->assertInstanceOf(
            Post::class,
            $request->post()
        );

        $this->assertInstanceOf(
            Post::class,
            Request::post()
        );

    }

    public function testPutMethodReturnAppropriateInstance()
    {

        $request = new Request();
        $this->assertInstanceOf(
            Put::class,
            $request->put()
        );

        $this->assertInstanceOf(
            Put::class,
            Request::put()
        );

    }

    public function testPatchMethodReturnAppropriateInstance()
    {

        $request = new Request();
        $this->assertInstanceOf(
            Patch::class,
            $request->patch()
        );

        $this->assertInstanceOf(
            Patch::class,
            Request::patch()
        );

    }

    public function testDeleteMethodReturnAppropriateInstance()
    {

        $request = new Request();
        $this->assertInstanceOf(
            Delete::class,
            $request->delete()
        );

        $this->assertInstanceOf(
            Delete::class,
            Request::delete()
        );

    }

}