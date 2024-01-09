<?php

namespace Feadmin\Exceptions;

use Exception;
use Throwable;

class MissingExtensionException extends Exception
{
    public function __construct(string $extension = '', int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf('Extension [%s] not found.', $extension);

        parent::__construct($message, $code, $previous);
    }
}
