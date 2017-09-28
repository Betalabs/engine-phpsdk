<?php

namespace Betalabs\Engine\Database;

class BootResponse
{

    /** @var bool */
    protected $success;

    /** @var string */
    protected $message;

    /**
     * BootResponse constructor.
     * @param bool $success
     * @param string $message
     */
    public function __construct($success, $message = null)
    {
        $this->success = $success;
        $this->message = $message;
    }

    /**
     * Format response into an array
     *
     * @return array
     */
    public function formatResponse()
    {

        $response = [
            'success' => $this->success
        ];

        if(is_null($this->message)) {
            return $response;
        }

        return array_merge($response, [
            'message' => $this->message
        ]);

    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * @return string|null
     */
    public function getMessage()
    {
        return $this->message;
    }

}