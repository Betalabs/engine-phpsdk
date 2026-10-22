<?php

namespace Betalabs\Engine\Tests;

use Betalabs\Engine\Auth\Token;
use Betalabs\Engine\Configs\Reader;
use Betalabs\Engine\Permissions\Boot as PermissionBoot;
use Betalabs\Engine\Requests\EndpointResolver;

class TestCase extends \PHPUnit\Framework\TestCase
{

    public function tearDown(): void {
        \Mockery::close();
        $this->clearStaticState();
    }

    /**
     * The SDK keeps endpoint, token, permissions and config in static
     * properties; reset them so no test depends on the execution order
     */
    protected function clearStaticState()
    {
        EndpointResolver::setEndpoint(null);
        PermissionBoot::clearPermissions();
        (new \ReflectionProperty(Token::class, 'accessToken'))->setValue(null, null);
        (new \ReflectionProperty(Reader::class, 'configObject'))->setValue(null, null);
    }

}
