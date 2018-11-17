<?php

namespace LaravelProcessRelations\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Masterfri\ProcessRelations\Eloquent\Concerns\CanProcessRelations;

class BookCard extends Model
{
    use CanProcessRelations;
    
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