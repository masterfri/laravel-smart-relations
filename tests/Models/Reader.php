<?php

namespace Masterfri\SmartRelations\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Masterfri\SmartRelations\SmartRelations;

class Reader extends Model
{
    use SmartRelations;
    
    public $timestamps = false;
    
    protected $fillable = ['name', 'subscriptions'];
    
    protected $cascade_delete = ['subscriptions'];
    
    public function subscriptions()
    {
        return $this->belongsToMany(Library::class, 'subscriptions');
    }
}