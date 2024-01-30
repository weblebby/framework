<?php

namespace Weblebby\Framework\Exceptions;

use Exception;
use Throwable;

class PostTypeAlreadyRegisteredException extends Exception
{
    public function __construct(string $name, int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf('Post type `%s` already registered.', $name);

        parent::__construct($message, $code, $previous);
    }
}
