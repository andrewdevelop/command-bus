<?php

namespace Core\Command;

use Exception;
use Throwable;

final class HandlerResolutionException extends Exception
{
    public function __construct(string $class, int $code = 0, Throwable $previous = null)
    {
        parent::__construct("Command handler \"$class\" not found.", $code, $previous);
    }
}