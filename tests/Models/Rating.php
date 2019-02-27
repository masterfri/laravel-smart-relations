<?php

namespace Masterfri\SmartRelations\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Masterfri\SmartRelations\SmartRelations;

class Rating extends Model
{
    use SmartRelations;
    
    public $timestamps = false;
    
    protected $fillable = ['score', 'subject'];
    
    public function subject()
    {
        return $this->morphTo();
    }
}