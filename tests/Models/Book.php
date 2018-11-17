<?php

namespace LaravelProcessRelations\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Masterfri\ProcessRelations\Eloquent\Concerns\CanProcessRelations;

class Book extends Model
{
    use CanProcessRelations;
    
    public $timestamps = false;
    
    protected $fillable = ['title', 'library', 'card'];
    
    protected $cascade_delete = ['card'];
    
    public function library()
    {
        return $this->belongsTo(Library::class);
    }
    
    public function card()
    {
        return $this->hasOne(BookCard::class);
    }
}