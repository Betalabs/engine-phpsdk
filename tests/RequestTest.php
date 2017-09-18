<?php

namespace Betalabs\Engine\Tests;

use Betalabs\Engine\Request\Methods\Delete;
use Betalabs\Engine\Request\Methods\Get;
use Betalabs\Engine\Request\Methods\Patch;
use Betalabs\Engine\Request\Methods\Post;
use Betalabs\Engine\Request\Methods\Put;
use PHPUnit\Framework\TestCase;
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