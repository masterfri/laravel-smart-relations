<?php

namespace Masterfri\SmartRelations\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Masterfri\SmartRelations\SmartRelations;

class Reader extends Model
{
    use SmartRelations;
    
    public $timestamps = false;
    
    protected $fillable = ['name', 'subscriptions', 'rating', 'reviews', 'tags'];
    
    protected $cascade_delete = ['subscriptions', 'rating', 'reviews', 'tags'];
    
    public function subscriptions()
    {
        return $this->belongsToMany(Library::class, 'subscriptions');
    }
    
    public function reviews()
    {
        return $this->morphMany(Review::class, 'subject');
    }
    
    public function rating()
    {
        return $this->morphOne(Rating::class, 'subject');
    }
    
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}