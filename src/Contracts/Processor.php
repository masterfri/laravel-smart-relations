<?php 

namespace Masterfri\SmartRelations\Contracts;

interface Processor
{
    /**
     * Assign value to relation on model
     * 
     * @param mixed $value
     * 
     * @return bool return true if relation needs to be saved after parent model is saved
     */ 
    public function assign($value);
    
    /**
     * Save relation
     */ 
    public function save();
    
    /**
     * Perform cascade delete on relation
     */ 
    public function cascadeDelete();
}