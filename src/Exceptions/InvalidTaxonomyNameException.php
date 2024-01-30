<?php

namespace Weblebby\Framework\Exceptions;

use Exception;
use Throwable;
use Weblebby\Framework\Contracts\Eloquent\PostInterface;

class InvalidTaxonomyNameException extends Exception
{
    /**
     * @param  class-string<int, PostInterface>  $model
     */
    public function __construct(string $model, int $code = 0, ?Throwable $previous = null)
    {
        $message = sprintf('Taxonomy name must start with `%s_` in `%s` model.', $model::getModelName(), $model);

        parent::__construct($message, $code, $previous);
    }
}
