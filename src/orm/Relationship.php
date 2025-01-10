<?php

namespace Sherpa\Trail\orm;

/**
 * Relationship Types enum.
 */
enum Relationship
{
    case BELONGS_TO;
    case HAS_MANY;
    case HAS_ONE;
    case MANY_TO_MANY;
}
