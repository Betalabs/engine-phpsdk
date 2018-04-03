<?php

namespace Betalabs\Engine\Auth;

class Header
{

    /** @var bool */
    protected $mustAuthorize = true;

    /** @var \Betalabs\Engine\Auth\Token */
    protected $token;

    public function __construct(Token $token)
    {
        $this->token = $token;
    }

    /**
     * Build header array with authenticate if required
     *
     * @return array
     * @throws \Betalabs\Engine\Auth\Exceptions\TokenExpiredException
     * @throws \Betalabs\Engine\Auth\Exceptions\UnauthorizedException
     * @throws \Betalabs\Engine\Configs\Exceptions\ClientNotDefinedException
     * @throws \Betalabs\Engine\Configs\Exceptions\ConfigDoesNotExistException
     * @throws \Betalabs\Engine\Configs\Exceptions\PropertyNotFoundException
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     * @throws \ReflectionException
     */
    public function header()
    {
        if(!$this->mustAuthorize) {
            return [];
        }

        return [
            'Authorization' => 'Bearer '. $this->token->retrieveToken()
        ];
    }

    /**
     * Define authorization is required
     *
     * @return $this
     */
    public function mustAuthorize()
    {
        $this->mustAuthorize = true;
        return $this;
    }

    /**
     * Define authorization is not required
     *
     * @return $this
     */
    public function mustNotAuthorize()
    {
        $this->mustAuthorize = false;
        return $this;
    }

}