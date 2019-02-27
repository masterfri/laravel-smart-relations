<?php

namespace Masterfri\SmartRelations\Processor;

use Masterfri\SmartRelations\Contracts\Processor;

class BelongsToMany extends AbstractProcessor implements Processor
{
    use Concerns\ConvertToCollection;
    
    /**
     * @inheritdoc
     */ 
    public function assign($value)
    {
        $this->setValue($this->toCollection($this->getRelation(), $value));
        return true;
    }
    
    /**
     * @inheritdoc
     */ 
    public function save()
    {
        foreach ($this->getValue() as $model) {
            if (!$model->exists) {
                $model->save();
            }
        }
        $this->getRelation()->sync($this->getValue());
    }
    
    /**
     * @inheritdoc
     */ 
    public function cascadeDelete()
    {
        $this->getRelation()->detach();
    }
}