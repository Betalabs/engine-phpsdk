<?php

namespace Betalabs\Engine\Tests\Auth;

use Betalabs\Engine\Auth\Exceptions\UnauthorizedException;
use Betalabs\Engine\Auth\Header;
use Betalabs\Engine\Auth\Token;
use PHPUnit\Framework\TestCase;

class HeaderTest extends TestCase
{

    public function testTokenIsReturnedWhenBearerTokenIsNotNull()
    {

        $token = \Mockery::mock(Token::class);
        $token->shouldReceive('request')
            ->times(0);

        $header = new Header($token);

        $bearerToken = 'bearer-token-hash';
        $header->setBearerToken($bearerToken);

        $this->assertEquals(
            $bearerToken,
            $header->retrieveToken()
        );

    }

    public function testTokenIsRequiredWhenBearerTokenIsNullAndEmailAndPasswordIsInformed()
    {

        $email = 'engine@betalabs';
        $password = 'password';
        $bearerToken = 'bearer-token-hash';

        $token = \Mockery::mock(Token::class);
        $token->shouldReceive('request')
            ->once()
            ->with($email, $password)
            ->andReturn($bearerToken);

        $header = new Header();
        $header->setToken($token);
        $header->setEmail($email);
        $header->setPassword($password);

        $this->assertEquals(
            $bearerToken,
            $header->retrieveToken()
        );

    }

    public function testTokenIsStoredAfterARequestIsSent()
    {

        $bearerToken = 'bearer-token-hash';

        $token = \Mockery::mock(Token::class);
        $token->shouldReceive('request')
            ->once()
            ->andReturn($bearerToken);

        $header = new Header();
        $header->setToken($token);
        $header->setEmail('engine@betalabs');
        $header->setPassword('password');

        $header->retrieveToken();

        $this->assertEquals(
            $bearerToken,
            $header->retrieveToken()
        );

    }

    public function testHeaderDoesNotIncludeAuthorizationWhenMustNotAuthorize()
    {

        $token = \Mockery::mock(Token::class);

        $header = new Header($token);

        $header->setBearerToken('bearer-token-hash');
        $header->mustNotAuthorize();

        $this->assertEmpty($header->header());

    }

    public function testHeaderIncludeAuthorizationWhenMustAuthorize()
    {

        $token = \Mockery::mock(Token::class);

        $header = new Header($token);

        $bearerToken = 'bearer-token-hash';
        $header->setBearerToken($bearerToken);

        $this->assertEquals([
            'Authorization' => 'Bearer '. $bearerToken
        ], $header->header());

    }

    public function testExceptionWhenNoTokenEmailOrPasswordIsInformed()
    {
        
        $this->expectException(UnauthorizedException::class);

        $token = \Mockery::mock(Token::class);

        $header = new Header($token);

        $header->retrieveToken();

    }

}