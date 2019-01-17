<?php

namespace Masterfri\SmartRelations\Processor\Concerns;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Collection;

trait ConvertToCollection
{
    use ConvertToModel;
    
    /**
     * Convert value to collection of models
     * 
     * @param Relation $relation
     * @param mixed $values
     * 
     * @return Collection
     */
    protected function toCollection(Relation $relation, $values)
    {
        if (empty($values)) {
            return new Collection();
        }
        
        if ($values instanceof Collection) {
            return $values;
        }
        
        $result = new Collection();
        foreach ($values as $value) {
            $value = $this->toModel($relation, $value);
            if ($value !== null) {
                $result->push($value);
            }
        }
        
        return $result;
    }
}