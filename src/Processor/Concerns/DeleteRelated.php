<?php

namespace Masterfri\SmartRelations\Processor\Concerns;

use Illuminate\Database\Eloquent\Relations\Relation;

trait DeleteRelated
{
    /**
     * Delete related records
     * 
     * @param Relation $relation
     * @param array $except
     */ 
    protected function deleteRelated(Relation $relation, $except = [])
    {
        $query = $relation->getQuery();
        
        if (count($except) !== 0) {
            $query = (clone $query)->whereNotIn($query->getModel()->getKeyName(), $except);
        }
        
        foreach ($query->cursor() as $model) {
            $model->delete();
        }
    }
}