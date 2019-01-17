<?php

namespace Masterfri\SmartRelations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use LogicException;
use ReflectionClass;

class RelationsManager
{
    /**
     * @var array
     */ 
    protected static $processors = [
        BelongsTo::class => Processor\BelongsTo::class,
        BelongsToMany::class => Processor\BelongsToMany::class,
        HasOne::class => Processor\HasOne::class,
        HasMany::class => Processor\HasMany::class,
    ];
    
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $pending;
    
    /**
     * @var Model
     */
    protected $model;
    
    /**
     * Constructor
     * 
     * @param Model $model
     */ 
    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->pending = collect([]);
    }
    
    /**
     * Return the model
     * 
     * @return Model
     */ 
    public function getModel()
    {
        return $this->model;
    }
    
    /**
     * Add/replace relation processor implementation
     * 
     * @param string $class
     * @param string $implementation
     */ 
    public static function extend($class, $implementation) 
    {
        if (!static::isValidImplementation($implementation)) {
            throw new LogicException(sprintf(
                '%s must implement %s interface', $implementation, Contracts\Processor::class
            ));
        }
        self::$processors[$class] = $implementation;
    }
    
    /**
     * Check if provided class has valid implementation
     * 
     * @param string $implementation
     * 
     * @return bool
     */ 
    protected static function isValidImplementation($implementation)
    {
        return (new ReflectionClass($implementation))->implementsInterface(Contracts\Processor::class);
    }
    
    /**
     * Create corresponding relation processor
     * 
     * @param string $attribute
     * 
     * @return Contracts\Processor
     */ 
    protected function getProcessor($attribute)
    {
        $relation = $this->getModel()->$attribute();
        
        if (! $relation instanceof Relation) {
            throw new LogicException(sprintf(
                '%s::%s must return a relationship instance', get_class($this->getModel()), $attribute
            ));
        }
        
        $class = get_class($relation);
        if (!array_key_exists($class, self::$processors)) {
            throw new LogicException(sprintf(
                '%s relationship is not supported yet', $class
            ));
        }
        
        return (new ReflectionClass(self::$processors[$class]))->newInstance($this->getModel(), $relation, $attribute);
    }
    
    /**
     * Make a relationship
     * 
     * @param string $attribute
     * @param mixed $value
     */ 
    public function assign($attribute, $value)
    {
        $processor = $this->getProcessor($attribute);
        if ($processor->assign($value)) {
            $this->pending->push($processor);
        }
    }
    
    /**
     * Save all pending relations
     */ 
    public function savePending()
    {
        foreach ($this->pending as $processor) {
            $processor->save();
        }
        $this->pending = collect([]);
    }
    
    /**
     * Perform cascade delete
     */ 
    public function cascadeDelete()
    {
        foreach ($this->getModel()->getRelationsForCascadeDelete() as $attribute) {
            $processor = $this->getProcessor($attribute);
            $processor->cascadeDelete();
        }
    }
}