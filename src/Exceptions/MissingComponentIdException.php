<?php

namespace Wirestrap\Exceptions;

use RuntimeException;

class MissingComponentIdException extends RuntimeException
{
    public function __construct(string $component, ?string $file = null, ?int $line = null)
    {
        $message = sprintf(
            'Missing ID: The [%s] component requires a stable "id" prop to function properly. '
            . 'Add an id attribute or disable strict mode in the configuration.',
            $component
        );

        parent::__construct($message);

        if ($file) {
            $this->file = $file;
        }

        if ($line) {
            $this->line = $line;
        }
    }
}
