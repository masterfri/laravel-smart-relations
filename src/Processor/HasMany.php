<?php

namespace Masterfri\SmartRelations\Processor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Masterfri\SmartRelations\Contracts\Processor;
use LogicException;

class HasMany extends AbstractProcessor implements Processor
{
    use Concerns\ConvertToCollection;
    use Concerns\DeleteRelated;
    
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
        if (!$this->getModel()->wasRecentlyCreated) {
            $actual = [];
            foreach ($this->getValue() as $model) {
                if ($model->exists) {
                    $actual[] = $model->getKey();
                }
            }
            $this->deleteRelated($this->getRelation(), $actual);
        }
        
        $this->getRelation()->saveMany($this->getValue());
    }
    
    /**
     * @inheritdoc
     */ 
    public function cascadeDelete()
    {
        $this->deleteRelated($this->getRelation());
    }
}