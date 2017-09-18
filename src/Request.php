<?php

namespace Betalabs\Engine;

use Betalabs\Engine\Request\Request as RealRequest;

class Request
{

    /**
     * Allows non-static calls to be correctly addressed
     *
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return $this->call($method, $arguments);
    }

    /**
     * Allows static calls to be correctly addressed
     *
     * @param $method
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($method, $arguments)
    {
        return (new static)->call($method, $arguments);
    }

    /**
     * Makes the call to the final object
     *
     * @param $method
     * @param $arguments
     * @return mixed
     */
    protected function call($method, $arguments)
    {
        $request = new RealRequest();
        return $request->$method(...$arguments);
    }

}