<?php

namespace Betalabs\Engine\Tests\Routes;

use Betalabs\Engine\Routes\Request;
use Betalabs\Engine\Tests\TestCase;
use Laminas\Diactoros\ServerRequest;
use Psr\Http\Message\UploadedFileInterface;

class RequestTest extends TestCase
{

    /** @var array */
    protected $originalGlobals;

    protected function setUp(): void
    {
        $this->originalGlobals = [$_SERVER, $_GET, $_POST, $_COOKIE, $_FILES];
    }

    public function tearDown(): void
    {
        [$_SERVER, $_GET, $_POST, $_COOKIE, $_FILES] = $this->originalGlobals;
        parent::tearDown();
    }

    public function testBuildRequestReturnsLaminasServerRequest()
    {

        $request = new Request();

        // Routes\Boot::start() type-hints the concrete Laminas class
        $this->assertInstanceOf(
            ServerRequest::class,
            $request->buildRequest()
        );

    }

    public function testBuildRequestUsesMethodAndUriFromServer()
    {

        $_SERVER = array_merge($_SERVER, [
            'REQUEST_METHOD' => 'POST',
            'HTTP_HOST' => 'app.engine.test',
            'REQUEST_URI' => '/boot/permission?page=2',
            'QUERY_STRING' => 'page=2',
            'HTTPS' => 'on',
        ]);

        $request = (new Request())->buildRequest();

        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('https', $request->getUri()->getScheme());
        $this->assertEquals('app.engine.test', $request->getUri()->getHost());
        $this->assertEquals('/boot/permission', $request->getUri()->getPath());
        $this->assertEquals('page=2', $request->getUri()->getQuery());

    }

    public function testBuildRequestUsesQueryBodyAndCookies()
    {

        $_GET = ['page' => '2', 'filter' => ['status' => 'active']];
        $_POST = ['name' => 'Engine', 'items' => [1, 2]];
        $_COOKIE = ['session' => 'abc123'];

        $request = (new Request())->buildRequest();

        $this->assertEquals(
            ['page' => '2', 'filter' => ['status' => 'active']],
            $request->getQueryParams()
        );
        $this->assertEquals(
            ['name' => 'Engine', 'items' => [1, 2]],
            $request->getParsedBody()
        );
        $this->assertEquals(
            ['session' => 'abc123'],
            $request->getCookieParams()
        );

    }

    public function testBuildRequestReadsEngineHeadersFromServer()
    {

        $_SERVER = array_merge($_SERVER, [
            'HTTP_ENGINE_ENDPOINT' => 'http://engine.endpoint',
            'HTTP_ENGINE_ACCESS_TOKEN' => 'token-hash',
            'HTTP_ENGINE_REFRESH_TOKEN' => 'refresh-hash',
            'HTTP_ENGINE_TOKEN_EXPIRES_AT' => '9999999',
        ]);

        $request = (new Request())->buildRequest();

        // Same header names read by Routes\Boot::engineHeaders()
        $this->assertEquals('http://engine.endpoint', $request->getHeaderLine('engine-endpoint'));
        $this->assertEquals('token-hash', $request->getHeaderLine('engine-access-token'));
        $this->assertEquals('refresh-hash', $request->getHeaderLine('engine-refresh-token'));
        $this->assertEquals('9999999', $request->getHeaderLine('engine-token-expires-at'));

    }

    public function testBuildRequestNormalizesUploadedFiles()
    {

        $_FILES = [
            'document' => [
                'name' => 'invoice.pdf',
                'type' => 'application/pdf',
                'tmp_name' => __FILE__,
                'error' => UPLOAD_ERR_OK,
                'size' => 1234,
            ],
        ];

        $files = (new Request())->buildRequest()->getUploadedFiles();

        $this->assertArrayHasKey('document', $files);
        $this->assertInstanceOf(UploadedFileInterface::class, $files['document']);
        $this->assertEquals('invoice.pdf', $files['document']->getClientFilename());
        $this->assertEquals('application/pdf', $files['document']->getClientMediaType());
        $this->assertEquals(1234, $files['document']->getSize());
        $this->assertEquals(UPLOAD_ERR_OK, $files['document']->getError());

    }

    public function testBuildRequestWithEmptyGlobals()
    {

        $_GET = [];
        $_POST = [];
        $_COOKIE = [];
        $_FILES = [];

        $request = (new Request())->buildRequest();

        $this->assertEquals([], $request->getQueryParams());
        $this->assertEquals([], $request->getCookieParams());
        $this->assertEquals([], $request->getUploadedFiles());

    }

}
