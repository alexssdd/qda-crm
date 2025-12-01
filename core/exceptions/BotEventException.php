<?php

namespace app\core\exceptions;

use Throwable;
use Exception;

/**
 * Exception
 */
class BotEventException extends Exception
{
    private $_data;

    // Redefine the exception so message isn't optional
    public function __construct($message, $data = [], $code = 0, Throwable $previous = null) {
        // some code

        $this->_data = $data;
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array|mixed
     */
    public function getData()
    {
        return $this->_data;
    }
}