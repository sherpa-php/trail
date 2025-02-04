<?php

namespace Sherpa\Trail\entities;

class Collection
{
    public private(set) array $models = [];

    public function first(): mixed
    {
        return count($this->models)
            ? $this->models[0]
            : null;
    }
}