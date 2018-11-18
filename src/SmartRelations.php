<?php

namespace Masterfri\SmartRelations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Support\Str;
use LogicException;

trait SmartRelations
{
    /**
     * @var array
     */ 
    protected $pending_relations = [];
    
    /**
     * @var array
     */ 
    // protected $cascade_delete = [];
    
    public static function bootSmartRelations()
    {
        static::saved(function ($model) {
            $model->savePendingRelations();
        });
        
        static::deleting(function ($model) {
            $model->cascadeDeleteRelations();
        });
    }
    
    /**
     * Set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        // First we will check for the presence of a mutator for the set operation
        // which simply lets the developers tweak the attribute as it is set on
        // the model, such as "json_encoding" an listing of data for storage.
        if ($this->hasSetMutator($key)) {
            return $this->setMutatedAttributeValue($key, $value);
        }

        // If an attribute is listed as a "date", we'll convert it from a DateTime
        // instance into a form proper for storage on the database tables using
        // the connection grammar's date format. We will auto set the values.
        elseif ($value && $this->isDateAttribute($key)) {
            $value = $this->fromDateTime($value);
        }

        if ($this->isJsonCastable($key) && ! is_null($value)) {
            $value = $this->castAttributeAsJson($key, $value);
        }

        // If this attribute contains a JSON ->, we'll set the proper value in the
        // attribute's underlying array. This takes care of properly nesting an
        // attribute in the array's value in the case of deeply nested items.
        if (Str::contains($key, '->')) {
            return $this->fillJsonAttribute($key, $value);
        }
        
        // If this is relation, we'll try to save it
        if ($this->relationDefined($key)) {
            return $this->setRelationValue($key, $value);
        }

        $this->attributes[$key] = $value;

        return $this;
    }
    
    /**
     * Check if model has the method for certain relationship
     * 
     * @param string $name
     * 
     * @return bool
     */ 
    protected function relationDefined($name)
    {
        return method_exists($this, $name);
    }
    
    /**
     * Set relationship
     * 
     * @param string $key
     * @param mixed $value
     * 
     * @return Model
     */ 
    protected function setRelationValue($key, $value)
    {
        $relation = $this->$key();

        if (! $relation instanceof Relation) {
            throw new LogicException(sprintf(
                '%s::%s must return a relationship instance.', static::class, $key
            ));
        }
        
        if ($relation instanceof BelongsTo) {
            return $this->setBelongsToRelationValue($relation, $key, $value);
        }
        
        if ($relation instanceof BelongsToMany) {
            return $this->setBelongsToManyRelationValue($relation, $key, $value);
        }
        
        if ($relation instanceof HasOne) {
            return $this->setHasOneRelationValue($relation, $key, $value);
        }
        
        if ($relation instanceof HasMany) {
            return $this->setHasManyRelationValue($relation, $key, $value);
        }
        
        throw new LogicException(sprintf(
            '%s relationship is not supported yet', get_class($relation)
        ));
    }
    
    /**
     * Set value for BelongsTo relation on model
     * 
     * @param BelongsTo $relation
     * @param string $key
     * @param mixed $value
     * 
     * @return Model
     */
    protected function setBelongsToRelationValue(BelongsTo $relation, $key, $value)
    {
        $value = $this->convertValueToEloquent($relation, $value);
        
        if ($value !== null) {
            if (!$value->exists) {
                throw new LogicException(sprintf(
                    'Trying to make relationship on %s with model that does not exist in database', $key
                ));
            }
            $relation->associate($value);
        } else {
            $relation->dissociate();
        }
        
        return $this;
    }
    
    /**
     * Set value for BelongsToMany relation on model
     * 
     * @param BelongsToMany $relation
     * @param string $key
     * @param mixed $value
     * 
     * @return Model
     */
    protected function setBelongsToManyRelationValue(BelongsToMany $relation, $key, $value)
    {
        $value = $this->convertValuesToEloquent($relation, $value);
        
        $this->setRelation($key, $value);
        $this->pending_relations[$key] = $relation;
        
        return $this;
    }
    
    /**
     * Set value for HasOne relation on model
     * 
     * @param HasOne $relation
     * @param string $key
     * @param mixed $value 
     * 
     * @return Model
     */
    protected function setHasOneRelationValue(HasOne $relation, $key, $value)
    {
        $value = $this->convertValueToEloquent($relation, $value);
        
        $this->setRelation($key, $value);
        $this->pending_relations[$key] = $relation;
        
        return $this;
    }
    
    /**
     * Set value for HasMany relation on model
     * 
     * @param HasMany $relation
     * @param string $key
     * @param mixed $value 
     * 
     * @return Model
     */
    protected function setHasManyRelationValue(HasMany $relation, $key, $value)
    {
        $value = $this->convertValuesToEloquent($relation, $value);
        
        $this->setRelation($key, $value);
        $this->pending_relations[$key] = $relation;
        
        return $this;
    }
    
    /**
     * Convert value to eloquent model instance
     * 
     * @param Relation $relation
     * @param mixed $value
     * 
     * @return Model|null
     */ 
    protected function convertValueToEloquent(Relation $relation, $value)
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
    
    /**
     * Convert value to collection of models
     * 
     * @param Relation $relation
     * @param mixed $values
     * 
     * @return Collection
     */
    protected function convertValuesToEloquent(Relation $relation, $values)
    {
        if (empty($values)) {
            return new Collection();
        }
        
        if ($values instanceof Collection) {
            return $values;
        }
        
        $result = new Collection();
        foreach ($values as $value) {
            $value = $this->convertValueToEloquent($relation, $value);
            if ($value !== null) {
                $result->push($value);
            }
        }
        return $result;
    }
    
    /**
     * Save relations that were queued
     */ 
    protected function savePendingRelations()
    {
        foreach ($this->pending_relations as $key => $relation) {
            $this->savePendingRelation($key, $relation);
        }
        $this->pending_relations = [];
    }
    
    /**
     * Save single queued relation
     * 
     * @param string $key
     * @param Relation $relation
     */
    protected function savePendingRelation($key, Relation $relation)
    {
        if ($this->relationLoaded($key)) {
            $value = $this->getRelation($key);
            
            if ($relation instanceof BelongsToMany) {
                $this->savePendingBelongsToManyRelation($relation, $value);
                return;
            }
            
            if ($relation instanceof HasOne) {
                $this->savePendingHasOneRelation($relation, $value);
                return;
            }
            
            if ($relation instanceof HasMany) {
                $this->savePendingHasManyRelation($relation, $value);
                return;
            }
        }
    }
    
    /**
     * Save values for BelongsToMany relation
     * 
     * @param BelongsToMany $relation
     * @param Collection $values
     */ 
    protected function savePendingBelongsToManyRelation(BelongsToMany $relation, Collection $values)
    {
        foreach ($values as $value) {
            if (!$value->exists) {
                $value->save();
            }
        }
        $relation->sync($values);
    }
    
    /**
     * Save values for HasOne relation
     * 
     * @param HasOne $relation
     * @param Model|null $value
     */ 
    protected function savePendingHasOneRelation(HasOne $relation, $value)
    {
        $previous = $this->wasRecentlyCreated ? null : $relation->getQuery()->first();
        
        if ($value === null) {
            if ($previous !== null) {
                $previous->delete();
            }
            return;
        }
        
        if ($previous !== null && $previous->getKey() != $value->getKey()) {
            $previous->delete();
        }
        
        $relation->save($value);
    }
    
    /**
     * Save values for HasMany relation
     * 
     * @param HasMany $relation
     * @param Collection $values
     */ 
    protected function savePendingHasManyRelation(HasMany $relation, Collection $values)
    {
        if (!$this->wasRecentlyCreated) {
            $actual = [];
            foreach ($values as $value) {
                if ($value->exists) {
                    $actual[] = $value->getKey();
                }
            }
            $this->deleteChildrenRecords($relation, $actual);
        }
        
        $relation->saveMany($values);
    }
    
    /**
     * Delete relationships
     */ 
    protected function cascadeDeleteRelations()
    {
        if (property_exists($this, 'cascade_delete')) {
            foreach ($this->cascade_delete as $key) {
                $relation = $this->$key();
                    
                if (! $relation instanceof Relation) {
                    throw new LogicException(sprintf(
                        '%s::%s must return a relationship instance.', static::class, $key
                    ));
                }
                
                if ($relation instanceof BelongsTo) {
                    throw new LogicException('Relation must not be BelongsTo');
                }
                
                if ($relation instanceof BelongsToMany) {
                    $relation->detach();
                    continue;
                }
                
                if ($relation instanceof HasOneOrMany) {
                    $this->deleteChildrenRecords($relation);
                    continue;
                }
            }
        }
    }
    
    /**
     * Delete children records on model
     * 
     * @param HasOneOrMany $relation
     * @param array $except
     */ 
    protected function deleteChildrenRecords(HasOneOrMany $relation, $except = [])
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