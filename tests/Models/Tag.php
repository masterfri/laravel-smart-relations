<?php

namespace Masterfri\SmartRelations\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Masterfri\SmartRelations\SmartRelations;

class Tag extends Model
{
    use SmartRelations;
    
    public $timestamps = false;
    
    protected $fillable = ['tag'];
    
    protected $cascade_delete = ['libraries', 'readers'];
    
    public function libraries()
    {
        return $this->morphedByMany(Library::class, 'taggable');
    }
    
    public function readers()
    {
        return $this->morphedByMany(Reader::class, 'taggable');
    }
}