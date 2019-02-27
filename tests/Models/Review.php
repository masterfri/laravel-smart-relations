<?php

namespace Masterfri\SmartRelations\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Masterfri\SmartRelations\SmartRelations;

class Review extends Model
{
    use SmartRelations;
    
    public $timestamps = false;
    
    protected $fillable = ['text', 'subject'];
    
    public function subject()
    {
        return $this->morphTo();
    }
}