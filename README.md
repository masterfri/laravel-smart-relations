# Laravel Smart Relations

This library allows to work with relations in the way as if they were attributes.
To make a relationship, you only need to assign a value (or values in case of mass assignment) to model and create/save it.
For now the following relation types are supported: BelongsTo, BelongsToMany, HasOne, HasMany.

# Installation

`composer require masterfri/laravel-smart-relations`

# Examples

```php
use Masterfri\SmartRelations\SmartRelations;

class Library extends Model
{
    // To enable smart relations add the following trait in your model
    use SmartRelations;
    
    // Relations can be listed in $fillable property to enable mass assignment
    protected $fillable = ['name', 'books', 'readers'];
    
    // If you want children records to be deleted along with parent record
    // you can list required relation names in $cascade_delete property.
    // For example, when library instance is deleted all related books
    // will be deleted as well, and readers will be detached (not deleted, since 
    // it is BelongsToMany relationship)
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
    use SmartRelations;
    
    protected $fillable = ['title', 'library'];
    
    public function library()
    {
        return $this->belongsTo(Library::class);
    }
    
    public function card()
    {
        return $this->hasOne(BookCard::class);
    }
}

class Reader extends Model
{
    use SmartRelations;
    
    protected $fillable = ['name'];
}

class BookCard extends Model
{
    use SmartRelations;
    
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

// HasMany relation example
// =========================
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
// or 
$library->books = [
    ['title' => 'First Book'], 
    new Book(['title' => 'Second Book']),
    $existingBook,
    123,
];
$library->save();

// HasOne relation example
// =========================
$book = Book::create([
    'title' => 'Third book',
    'card' => new BookCard(),
    // or simply
    // 'card' => [],
]);

// BelongsTo relation example
// =========================
$book = Book::create([
    'title' => 'Third book',
    // In that way model can be associated with parent record
    'library' => $library,
]);
// or
$book->library = $library;
$book->save();

// BelongsToMany relation example
// =========================
$reader1 = Reader::create(['name' => 'John']);
$reader2 = Reader::create(['name' => 'Jack']);

// In case of many to many relation you can pass model instance/ID to make relationship 
$library->readers = [$reader1, $reader2->id];
$library->save();

// Update data on related records
// =========================
// If you pass primary key in the data, related records will be loaded
// from database, and their attributes will be updated. But bear in mind
// that related records which are not listed in the array will be deleted,
// because all children records will be replaced with new data.
$library->books = [
    ['id' => 1, 'title' => 'First Book New Name'], 
    ['id' => 2, 'title' => 'Second Book New Name'], 
];
