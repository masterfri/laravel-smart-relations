<?php

namespace Masterfri\SmartRelations\Processor\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

trait ConvertToModel
{
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
        if (empty($value) && is_scalar($value)) {
            return null;
        }
        
        if ($value instanceof Model) {
            return $value;
        }
        
        $related = $relation->getRelated();
        
        if (is_array($value)) {
            $pk = $related->getKeyName();
            if (isset($value[$pk])) {
                $instance = $related->newQuery()->findOrNew($value[$pk]);
                $instance->fill($value);
            } else {
                $instance = $related->newInstance($value);
            }
            return $instance;
        }
        
        return $related->find($value);
    }
}