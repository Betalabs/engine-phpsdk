<?php

namespace Betalabs\Engine\Tests\Requests\Methods;

use Betalabs\Engine\Auth\Header as AuthHeader;
use Betalabs\Engine\Auth\Token;
use Betalabs\Engine\Requests\EndpointResolver;
use Betalabs\Engine\Requests\Header;
use Betalabs\Engine\Requests\Methods\Delete;
use Betalabs\Engine\Requests\Methods\Get;
use Betalabs\Engine\Requests\Methods\Patch;
use Betalabs\Engine\Requests\Methods\Post;
use Betalabs\Engine\Requests\Methods\Put;
use Betalabs\Engine\Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Methods with a real Guzzle client: only the network (MockHandler), the
 * token and the endpoint are faked
 */
class HttpTest extends TestCase
{

    /** @var array */
    protected $history = [];

    /** @var \GuzzleHttp\Handler\MockHandler */
    protected $mockHandler;

    public static function methodsWithBody()
    {
        return [
            'post' => [Post::class, 'POST'],
            'put' => [Put::class, 'PUT'],
            'patch' => [Patch::class, 'PATCH'],
            'delete' => [Delete::class, 'DELETE'],
        ];
    }

    public function testGetSendsRequestToEndpointWithDefaultHeaders()
    {

        $get = $this->method(Get::class, new Response(200, [], '{"data":{"id":1}}'));

        $contents = $get->send('products?page=2');

        $request = $this->lastRequest();

        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('https://engine.test/api/products?page=2', (string) $request->getUri());
        $this->assertEquals('application/json', $request->getHeaderLine('Content-Type'));
        $this->assertEquals('application/json', $request->getHeaderLine('Accept'));
        $this->assertEquals('Bearer token-hash', $request->getHeaderLine('Authorization'));
        $this->assertEquals('', (string) $request->getBody());

        $this->assertEquals(1, $contents->data->id);
        $this->assertEquals($contents, $get->getContents());
        $this->assertEquals(200, $get->statusCode());
        $this->assertSame(200, $get->getResponse()->getStatusCode());

    }

    #[DataProvider('methodsWithBody')]
    public function testMethodSendsDataAsJsonBody($class, $httpMethod)
    {

        $method = $this->method($class, new Response(201, [], '{"data":{"saved":true}}'));

        $contents = $method->send('products/1', ['name' => 'Engine', 'tags' => ['a', 'b']]);

        $request = $this->lastRequest();

        $this->assertEquals($httpMethod, $request->getMethod());
        $this->assertEquals('https://engine.test/api/products/1', (string) $request->getUri());
        $this->assertEquals(
            ['name' => 'Engine', 'tags' => ['a', 'b']],
            json_decode((string) $request->getBody(), true)
        );
        $this->assertEquals('Bearer token-hash', $request->getHeaderLine('Authorization'));

        $this->assertTrue($contents->data->saved);
        $this->assertEquals(201, $method->statusCode());

    }

    public function testMustNotAuthorizeSendsNoAuthorizationHeader()
    {

        $token = \Mockery::mock(Token::class);
        $token->shouldNotReceive('retrieveToken');

        $get = $this->method(Get::class, new Response(200, [], '{}'), $token);

        $get->mustNotAuthorize()->send('public');

        $this->assertFalse($this->lastRequest()->hasHeader('Authorization'));

    }

    public function testMustAuthorizeAfterMustNotAuthorizeSendsAuthorizationHeader()
    {

        $get = $this->method(Get::class, new Response(200, [], '{}'));

        $get->mustNotAuthorize()->mustAuthorize()->send('private');

        $this->assertEquals('Bearer token-hash', $this->lastRequest()->getHeaderLine('Authorization'));

    }

    public function testEndpointWithoutSuffix()
    {

        $get = $this->method(Get::class, new Response(200, [], '{}'));

        $get->setEndpointSuffix(null)->send('health');

        $this->assertEquals('https://engine.test/health', (string) $this->lastRequest()->getUri());

    }

    public function testEndpointWithCustomSuffix()
    {

        $get = $this->method(Get::class, new Response(200, [], '{}'));

        $get->setEndpointSuffix('/v2/')->send('products');

        $this->assertEquals('https://engine.test/v2/products', (string) $this->lastRequest()->getUri());

    }

    public function testNonJsonResponseReturnsNull()
    {

        $get = $this->method(Get::class, new Response(200, [], 'not json'));

        $this->assertNull($get->send('products'));
        $this->assertEquals(200, $get->statusCode());

    }

    public function testClientErrorResponseThrowsClientException()
    {

        $get = $this->method(Get::class, new Response(404, [], '{"message":"Not found"}'));

        $this->expectException(ClientException::class);

        $get->send('products/999');

    }

    public function testServerErrorResponseThrowsServerException()
    {

        $post = $this->method(Post::class, new Response(500, [], '{"message":"Error"}'));

        $this->expectException(ServerException::class);

        $post->send('products', ['name' => 'Engine']);

    }

    protected function method($class, Response $response, $token = null)
    {

        $this->mockHandler = new MockHandler([$response]);

        $stack = HandlerStack::create($this->mockHandler);
        $stack->push(Middleware::history($this->history));

        if (is_null($token)) {
            $token = \Mockery::mock(Token::class);
            $token->shouldReceive('retrieveToken')
                ->andReturn('token-hash');
        }

        $endpoint = \Mockery::mock(EndpointResolver::class);
        $endpoint->shouldReceive('endpoint')
            ->andReturn('https://engine.test/');

        return new $class(
            new Client(['handler' => $stack]),
            new Header(new AuthHeader($token)),
            $endpoint
        );

    }

    protected function lastRequest()
    {

        $this->assertNotEmpty($this->history, 'No request was sent');

        return end($this->history)['request'];

    }

}
