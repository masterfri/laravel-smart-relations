<?php

namespace Masterfri\SmartRelations\Processor;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

abstract class AbstractProcessor
{
    /**
     * @var Model
     */
    protected $model;
    
    /**
     * @var Relation
     */
    protected $relation;
    
    /**
     * @var string
     */ 
    protected $attribute;
    
    /**
     * Constructor
     * 
     * @param Model $model
     * @param Relation $relation
     * @param string $attribute
     */
    public function __construct(Model $model, Relation $relation, $attribute)
    {
        $this->model = $model;
        $this->relation = $relation;
        $this->attribute = $attribute;
    }
    
    /**
     * Get the processed model
     * 
     * @return Model
     */ 
    protected function getModel()
    {
        return $this->model;
    }
    
    /**
     * Get the processed relation
     * 
     * @return Relation
     */
    protected function getRelation()
    {
        return $this->relation;
    }
    
    /**
     * Set value on relation
     * 
     * @param Model|\Illuminate\Database\Eloquent\Collection $value
     */ 
    protected function setValue($value)
    {
        $this->getModel()->setRelation($this->attribute, $value);
    }
    
    /**
     * Get value from the relation
     * 
     * @return Model|\Illuminate\Database\Eloquent\Collection
     */ 
    protected function getValue()
    {
        return $this->getModel()->getRelation($this->attribute);
    }
}