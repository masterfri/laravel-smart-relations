<?php

namespace LaravelProcessRelations\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Masterfri\ProcessRelations\Eloquent\Concerns\CanProcessRelations;

class Library extends Model
{
    use CanProcessRelations;
    
    public $timestamps = false;
    
    protected $fillable = ['name', 'books', 'readers'];
    
    protected $cascade_delete = ['books', 'readers'];
    
    public function books()
    {
        return $this->hasMany(Book::class);
    }
    
    public function readers()
    {
        return $this->belongsToMany(Reader::class, 'subscriptions');
    }
}