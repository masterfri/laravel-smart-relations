<?php

namespace Masterfri\SmartRelations\Processor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Masterfri\SmartRelations\Contracts\Processor;
use LogicException;

class BelongsTo extends AbstractProcessor implements Processor
{
    use Concerns\ConvertToModel;
    
    /**
     * @inheritdoc
     */ 
    public function assign($value)
    {
        $model = $this->toModel($this->getRelation(), $value);
        
        if ($model !== null) {
            if (!$model->exists) {
                throw new LogicException('Related model has to be saved first');
            }
            $this->getRelation()->associate($model);
        } else {
            $this->getRelation()->dissociate();
        }
        
        return false;
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
            sprintf('Attempt to perform cascade delete on %s', get_class($this->getRelation()))
        );
    }
}