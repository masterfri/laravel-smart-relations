<?php

namespace Masterfri\SmartRelations\Tests\Processor;

use Masterfri\SmartRelations\Processor\HasMany;

class Extended extends HasMany
{
    /**
     * @inheritdoc
     */ 
    public function save()
    {
        // Do not replace old children records, just add new
        $this->getRelation()->saveMany($this->getValue());
    }
}
