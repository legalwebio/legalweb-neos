<?php

declare(strict_types=1);

namespace LegalWeb\GdprTools\Exception;

use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Proxy(false)
 */
class InvalidDatasetException extends UpdateFailedException
{
    /**
     * @param string[] $errors
     */
    public function __construct(array $errors, int $code)
    {
        parent::__construct('Invalid dataset: ' . implode(' ', $errors), $code);
    }
}
