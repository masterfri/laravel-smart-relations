<?php

namespace Masterfri\SmartRelations\Processor\Concerns;

trait CheckNull
{
    /**
     * Check if the value is the equivalent of null
     * 
     * @param mixed $value
     * 
     * @return bool
     */ 
    protected function isNullEquivalent($value)
    {
        return $value === null || is_scalar($value) && empty($value);
    }
}