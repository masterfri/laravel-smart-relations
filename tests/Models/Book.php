<?php

namespace Masterfri\SmartRelations\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Masterfri\SmartRelations\SmartRelations;

class Book extends Model
{
    use SmartRelations;
    
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