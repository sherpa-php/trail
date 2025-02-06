<?php

namespace Sherpa\Trail\orm\exceptions;

use Sherpa\Exceptions\exceptions\SherpaException;
use Throwable;

/**
 * Sherpa Exception
 * <p>
 *     To throw if given relationship method
 *     does no longer exist on given model class.
 * </p>
 */
class RelationshipDoesNotExistOnModelException extends SherpaException
{
    public function __construct(string $relationship,
                                string $model,
                                ?Throwable $previous = null)
    {
        $message = "$relationship() relationship does no longer exist on $model model.";

        parent::__construct($message, 1252, $previous);
    }
}