<?php

namespace Masterfri\SmartRelations\Processor;

use Illuminate\Database\Eloquent\Model;
use Masterfri\SmartRelations\Contracts\Processor;
use LogicException;

abstract class BaseBelongsTo extends AbstractProcessor implements Processor
{
    /**
     * @inheritdoc
     */ 
    public function assign($value)
    {
        $this->makeAssociation($this->toModel($this->getRelation(), $value));
        return false;
    }
    
    /**
     * Make association with parent model
     * 
     * @param Model $model
     */ 
    public function makeAssociation(?Model $model)
    {
        if ($model !== null) {
            if (!$model->exists) {
                throw new LogicException(
                    'Related model has to be saved first'
                );
            }
            $this->getRelation()->associate($model);
        } else {
            $this->getRelation()->dissociate();
        }
    }
    
    /**
     * @inheritdoc
     */ 
    public function save()
    {
        throw new LogicException(
            sprintf('You should not call %s::save', static::class)
        );
    }
    
    /**
     * @inheritdoc
     */ 
    public function cascadeDelete()
    {
        throw new LogicException(
            sprintf('You should not call %s::cascadeDelete', static::class)
        );
    }
}