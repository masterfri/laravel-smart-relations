<?php

namespace Masterfri\SmartRelations\Processor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Masterfri\SmartRelations\Contracts\Processor;
use LogicException;

class HasOne extends AbstractProcessor implements Processor
{
    use Concerns\ConvertToModel;
    use Concerns\DeleteRelated;
    
    /**
     * @inheritdoc
     */ 
    public function assign($value)
    {
        $this->setValue($this->toModel($this->getRelation(), $value));
        return true;
    }
    
    /**
     * @inheritdoc
     */ 
    public function save()
    {
        $previous = $this->getModel()->wasRecentlyCreated ? null : $this->getRelation()->getQuery()->first();
        
        if ($this->getValue() === null) {
            if ($previous !== null) {
                $previous->delete();
            }
            return;
        }
        
        if ($previous !== null && $previous->getKey() !== $this->getValue()->getKey()) {
            $previous->delete();
        }
        
        $this->getRelation()->save($this->getValue());
    }
    
    /**
     * @inheritdoc
     */ 
    public function cascadeDelete()
    {
        $this->deleteRelated($this->getRelation());
    }
}