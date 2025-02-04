<?php

namespace Sherpa\Trail\entities;

/**
 * Sherpa models collection class.
 * <p>
 *     To use ONLY to contain models instances.
 * </p>
 * <p>
 *     A collection cannot be modified.
 * </p>
 */
class Collection
{
    public private(set) array $models;

    public function __construct(array $models = [])
    {
        $this->models = $models;
    }

    /**
     * @return mixed First collection's model instance
     */
    public function first(): mixed
    {
        return count($this->models)
            ? $this->models[0]
            : null;
    }

    /**
     * @return mixed Last collection's model instance
     */
    public function last(): mixed
    {
        $clone = array_slice($this->models, 0);

        return array_pop($clone);
    }
}