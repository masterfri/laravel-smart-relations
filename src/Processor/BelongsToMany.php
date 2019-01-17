<?php

namespace Masterfri\SmartRelations\Processor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Masterfri\SmartRelations\Contracts\Processor;
use LogicException;

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