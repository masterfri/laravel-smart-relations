<?php

namespace Masterfri\SmartRelations\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Masterfri\SmartRelations\SmartRelations;

class BookCard extends Model
{
    use SmartRelations;
    
    public $timestamps = false;
    
    protected $fillable = ['book', 'reader'];
    
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
    
    public function reader()
    {
        return $this->belongsTo(Reader::class);
    }
}