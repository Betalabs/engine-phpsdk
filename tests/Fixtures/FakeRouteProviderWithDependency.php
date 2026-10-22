<?php

namespace Betalabs\Engine\Tests\Fixtures;

use Aura\Router\Map;
use Betalabs\Engine\RouteProvider;

class FakeRouteProviderWithDependency implements RouteProvider
{

    /** @var \Betalabs\Engine\Tests\Fixtures\FakeDependency */
    public $dependency;

    /**
     * FakeRouteProviderWithDependency constructor.
     * @param \Betalabs\Engine\Tests\Fixtures\FakeDependency $dependency
     */
    public function __construct(FakeDependency $dependency)
    {
        $this->dependency = $dependency;
    }

    /**
     * @param \Aura\Router\Map $map
     * @return void
     */
    public function route(Map $map)
    {

    }

}
