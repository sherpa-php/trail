<?php

namespace Sherpa\Trail\orm\exceptions;

use Sherpa\Exceptions\exceptions\SherpaException;
use Throwable;

/**
 * Sherpa Exception
 * <p>
 *     To throw if given relationship enum value
 *     is no longer supported by current Sherpa ORM
 *     framework.
 * </p>
 */
class InvalidRelationshipException extends SherpaException
{
    public function __construct(mixed $relationship,
                                ?Throwable $previous = null)
    {
        $message = "$relationship relationship is not supported.";

        parent::__construct($message, 1251, $previous);
    }
}