<?php

namespace Betalabs\Engine\Tests\Auth;

use Betalabs\Engine\Auth\Token;
use Betalabs\Engine\Request\Methods\Post;
use Mockery;
use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase
{

    public function testRequestIsSendWithAppropriateData()
    {

        $clientId = 1;
        $clientSecret = 'secret-hash';
        $email = 'betalabs@engine';
        $password = 'password';
        $accessToken = 'access-token-hash';
        $refreshToken = 'refresh-token-hash';

        $post = $this->mockPost(
            $clientId,
            $clientSecret,
            $email,
            $password,
            $accessToken,
            $refreshToken
        );

        $token = new Token($post);

        $token->setClientId($clientId);
        $token->setClientSecret($clientSecret);

        $this->assertEquals(
            $accessToken,
            $token->request($email, $password)
        );

        $this->assertEquals(
            $accessToken,
            $token->accessToken()
        );

        $this->assertEquals(
            $refreshToken,
            $token->refreshToken()
        );

    }

    protected function mockPost(
        $clientId,
        $clientSecret,
        $email,
        $password,
        $accessToken,
        $refreshToken
    ) {
        $post = Mockery::mock(Post::class);
        $post->shouldReceive('mustNotAuthorize')
            ->once()
            ->andReturn($post);
        $post->shouldReceive('send')
            ->once()
            ->with(
                'oauth/token',
                [
                    'grant_type' => 'password',
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'username' => $email,
                    'password' => $password,
                    'scope' => '*'
                ],
                false
            );
        $post->shouldReceive('getContents')
            ->times(2)
            ->andReturn((object)[
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken
            ]);
        return $post;
    }

}