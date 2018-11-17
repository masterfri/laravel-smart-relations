# Laravel Process Relations

With this library you can work with relations through the same API as for attributes. 
For now the following relation types are supported: BelongsTo, BelongsToMany, HasOne, HasMany.

# Examples

```php
<?php

use Masterfri\ProcessRelations\Eloquent\Concerns\CanProcessRelations;

class Library extends Model
{
    use CanProcessRelations;
    
    // Relations can be listed in $fillable property to enable mass assignment
    protected $fillable = ['name', 'books', 'readers'];
    
    // Relations can be listed in $cascade_delete property, 
    // so related records will be deleted/detached when parent record gets deleted
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

class Book extends Model
{
    use CanProcessRelations;
    
    protected $fillable = ['title', 'library'];
    
    public function library()
    {
        return $this->belongsTo(Library::class);
    }
}

class Reader extends Model
{
    use CanProcessRelations;
    
    protected $fillable = ['name'];
}

..............

$library = Library::create([
    'name' => 'Central Library',
    // Related records can be passed as array or model instance
    // If instances does not exist in database, they will be created
    // You also can pass ID of existing record to attach it
    'books' => [
        ['title' => 'First Book'], 
        new Book(['title' => 'Second Book']),
        $existingBook,
        123,
    ],
]);

$book = Book::create([
    'title' => 'Third book',
    // In that way model can be associated with parent record
    'library' => $library,
]);

$reader1 = Reader::create(['name' => 'John']);
$reader2 = Reader::create(['name' => 'Jack']);

// In case of many to many relation you can pass model instance/ID to make relationship 
$library->readers = [$reader1, $reader2->id];
$library->save();
