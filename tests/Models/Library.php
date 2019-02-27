<?php

namespace Masterfri\SmartRelations\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Masterfri\SmartRelations\SmartRelations;

class Library extends Model
{
    use SmartRelations;
    
    public $timestamps = false;
    
    protected $fillable = ['name', 'books', 'readers', 'rating', 'reviews', 'tags'];
    
    protected $cascade_delete = ['books', 'readers', 'rating', 'reviews', 'tags'];
    
    public function books()
    {
        return $this->hasMany(Book::class);
    }
    
    public function readers()
    {
        return $this->belongsToMany(Reader::class, 'subscriptions');
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