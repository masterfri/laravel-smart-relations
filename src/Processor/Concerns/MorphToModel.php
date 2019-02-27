<?php

namespace Masterfri\SmartRelations\Processor\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Masterfri\SmartRelations\Exception\MalformedDataException;
use Masterfri\SmartRelations\Exception\IntegrityException;
use Illuminate\Support\Arr;

trait MorphToModel
{
    use CheckNull;
    
    /**
     * Convert value to eloquent model instance
     * 
     * @param Relation $relation
     * @param mixed $value
     * 
     * @return Model|null
     */ 
    protected function toModel(Relation $relation, $value)
    {
        if ($this->isNullEquivalent($value)) {
            return null;
        }
        
        if ($value instanceof Model) {
            return $value;
        }
        
        if (is_array($value) && Arr::has($value, ['id', 'type'])
            && is_scalar($value['id']) && is_string($value['type'])) {
            return $this->findByPk($value['id'], $value['type']);
        }
        
        throw new MalformedDataException('Unexpected data format');
    }
    
    /**
     * Retrieve model by its primary key
     * 
     * @param array $pk
     * @param string $type
     * 
     * @return Model|null
     */ 
    protected function findByPk($pk, $type)
    {
        $class = Relation::getMorphedModel($type);
        
        if ($class === null) {
            if (is_subclass_of($type, Model::class)) {
                $class = $type;
            } else {
                throw new MalformedDataException(
                    sprintf('Type %s is not mapped to any known model class', $type)
                );
            }
        }
        
        $model = $class::find($pk);
        
        if ($model === null) {
            throw new IntegrityException(
                sprintf('Related model does not exist: %s', $pk)
            );
        }
        
        return $model;
    }
}