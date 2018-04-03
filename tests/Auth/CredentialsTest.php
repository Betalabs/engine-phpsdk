<?php

namespace Betalabs\Engine\Tests\Auth;

use Betalabs\Engine\Auth\Credentials;
use Betalabs\Engine\Tests\TestCase;

class CredentialsTest extends TestCase
{

    public function testIsValidWithoutValuesDeclaredShouldReturnFalse()
    {
        $this->assertFalse(Credentials::isValid());
    }

    public function testIsValidWithSomeValuesDeclaredShouldReturnFalse()
    {
        Credentials::$apiUri = 'http://teste.engine';
        Credentials::$secret = '123abc';

        $this->assertFalse(Credentials::isValid());
    }

    public function testIsValidWithAllValuesDeclaredShouldReturnTrue()
    {
        Credentials::$identifier = 'Engine-PhpSDK';
        Credentials::$username = 'teste@teste.com';
        Credentials::$password = '123456';
        Credentials::$apiUri = 'http://php-sdk.engine';
        Credentials::$id = 1;
        Credentials::$secret = '123abc';

        $this->assertTrue(Credentials::isValid());
    }

    /**
     * @throws \ReflectionException
     */
    public function testCleaningCredentials()
    {
        Credentials::$identifier = 'Engine-PhpSDK';
        Credentials::$username = 'teste@teste.com';
        Credentials::$password = '123456';
        Credentials::$apiUri = 'http://php-sdk.engine';
        Credentials::$id = 1;
        Credentials::$secret = '123abc';

        Credentials::clear();
        $obj = Credentials::retrieve();

        $this->assertNull($obj->identifier);
        $this->assertNull($obj->apiUri);
        $this->assertNull($obj->id);
        $this->assertNull($obj->secret);
    }

    /**
     * @throws \ReflectionException
     */
    public function testRetrieve()
    {
        $identifier = 'Engine-PhpSDK';
        $apiUri = 'http://php-sdk.engine';
        $username = 'teste@teste.com';
        $password = '123456';
        $clientId = 1;
        $clientSecret = '123abc';

        Credentials::$identifier = $identifier;
        Credentials::$apiUri = $apiUri;
        Credentials::$username = $username;
        Credentials::$password = $password;
        Credentials::$id = $clientId;
        Credentials::$secret = $clientSecret;
        $obj = Credentials::retrieve();

        $this->assertInstanceOf(\stdClass::class, $obj);
        $this->assertEquals($identifier, $obj->identifier);
        $this->assertEquals($apiUri, $obj->apiUri);
        $this->assertEquals($clientId, $obj->id);
        $this->assertEquals($clientSecret, $obj->secret);
    }

    public function tearDown()
    {
        parent::tearDown();

        Credentials::clear();
    }
}
