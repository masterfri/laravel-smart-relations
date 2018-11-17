<?php

namespace LaravelProcessRelations\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Masterfri\ProcessRelations\Eloquent\Concerns\CanProcessRelations;

class Reader extends Model
{
    use CanProcessRelations;
    
    public $timestamps = false;
    
    protected $fillable = ['name', 'subscriptions'];
    
    protected $cascade_delete = ['subscriptions'];
    
    public function subscriptions()
    {
        return $this->belongsToMany(Library::class, 'subscriptions');
    }
}