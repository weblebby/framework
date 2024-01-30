<?php

namespace Weblebby\Framework\Exceptions;

use Exception;
use Throwable;

class PanelNotFoundException extends Exception
{
    public function __construct(string $panelName = '', int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf('Panel [%s] not found.', $panelName);

        parent::__construct($message, $code, $previous);
    }
}
