<?php

namespace Masterfri\SmartRelations;

use Illuminate\Support\Str;

trait SmartRelations
{
    /**
     * @var array
     */ 
    protected $relations_manager;
    
    /**
     * @var array
     */ 
    // protected $cascade_delete = [];
    
    public static function bootSmartRelations()
    {
        static::saved(function ($model) {
            $model->getRelationsManager()->savePending();
        });
        
        static::deleting(function ($model) {
            $model->getRelationsManager()->cascadeDelete();
        });
    }
    
    /**
     * Get relations manager
     * 
     * @return RelationsManager
     */ 
    protected function getRelationsManager()
    {
        if ($this->relations_manager === null) {
            $this->relations_manager = new RelationsManager($this);
        }
        return $this->relations_manager;
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
        if ($this->isRelationAttribute($key)) {
            return $this->getRelationsManager()->assign($key, $value);
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
    protected function isRelationAttribute($name)
    {
        return method_exists($this, $name);
    }
    
    /**
     * Get relations that have to be deleted recursively
     * 
     * @return array
     */ 
    public function getRelationsForCascadeDelete()
    {
        if (property_exists($this, 'cascade_delete')) {
            return $this->cascade_delete;
        }
        return [];
    }
}