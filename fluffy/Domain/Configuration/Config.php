<?php

namespace Fluffy\Domain\Configuration;

class Config
{
    public array $values = [];

    public function addArray(array $config)
    {
        $this->values = array_merge($this->values, $config);
    }
}