<?php

namespace Betalabs\Engine\Tests\Requests\Methods;

use Betalabs\Engine\Requests\EndpointResolver;
use Betalabs\Engine\Requests\Methods\Get;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Betalabs\Engine\Requests\Header;
use Betalabs\Engine\Tests\TestCase;
use GuzzleHttp\Client;

class GetTest extends TestCase
{

    public function testGetMethod()
    {

        $client = $this->mockClient();

        $header = \Mockery::mock(Header::class);
        $header->shouldReceive('headers')
            ->andReturn([
                'header-key' => 'header-value'
            ]);

        $endpoint = \Mockery::mock(EndpointResolver::class);
        $endpoint->shouldReceive('endpoint')
            ->andReturn('http://test.local/');

        $get = new Get($client, $header, $endpoint);

        $this->assertEquals(
            (object)[
                'data' => (object)[
                    'one' => 'field1',
                    'two' => 'field2',
                    'three' => 'field3'
                ]
            ],
            $get->send('path/to/api')
        );

    }

    protected function mockClient()
    {
        $stream = \Mockery::mock(StreamInterface::class);
        $stream->shouldReceive('getContents')
            ->once()
            ->andReturn(json_encode([
                'data' => [
                    'one' => 'field1',
                    'two' => 'field2',
                    'three' => 'field3'
                ]
            ]));

        $response = \Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('getBody')
            ->once()
            ->andReturn($stream);

        $client = \Mockery::mock(Client::class);
        $client->shouldReceive('get')
            ->once()
            ->with('http://test.local/api/path/to/api', [
                'headers' => ['header-key' => 'header-value']
            ])
            ->andReturn($response);

        return $client;
    }

}