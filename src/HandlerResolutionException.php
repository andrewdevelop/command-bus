<?php

namespace Core\Command;

use Exception;
use Throwable;

class HandlerResolutionException extends Exception
{
    public function __construct($class, $code = 0, Throwable $previous = null)
    {
        $message = "Command handler \"$class\" not found.";
        parent::__construct($message, $code, $previous);
    }
}