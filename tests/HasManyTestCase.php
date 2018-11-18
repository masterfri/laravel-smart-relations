<?php

namespace Masterfri\SmartRelations\Tests;

class HasManyTestCase extends TestCase
{
    public function testCanCreateNewRecords()
    {
        $library = Models\Library::create([
            'name' => 'Central Library',
            'books' => [
                ['title' => 'First Book'], 
                new Models\Book(['title' => 'Second Book']),
            ],
        ]);
        
        $library = $library->fresh();
        
        $this->assertCount(2, $library->books);
    }
    
    public function testCanAttachExistingRecords()
    {
        $book1 = Models\Book::create([
            'title' => 'First book',
        ]);
        
        $book2 = Models\Book::create([
            'title' => 'Second book',
        ]);
        
        $book3 = Models\Book::create([
            'title' => 'Third book',
        ]);
        
        $library = Models\Library::create([
            'name' => 'Central Library',
            'books' => [
                $book1, 
                $book2->id,
                ['id' => $book3->id],
            ],
        ]);
        
        $library = $library->fresh();
        
        $this->assertCount(3, $library->books);
    }
    
    public function testCanUpdateRelatedRecords()
    {
        $library = Models\Library::create([
            'name' => 'Central Library',
        ]);
        
        $book = Models\Book::create([
            'title' => 'First book',
            'library' => $library,
        ]);
        
        $library->books = [
            ['id' => $book->id, 'title' => 'New name'],
        ];
        $library->save();
        
        $book = $book->fresh();
        
        $this->assertEquals('New name', $book->title);
    }
    
    public function testCanReplaceRelatedRecords()
    {
        $library = Models\Library::create([
            'name' => 'Central Library',
            'books' => [
                ['title' => 'First Book'], 
                ['title' => 'Second Book'], 
            ],
        ]);
        
        $library = $library->fresh();
        $book = $library->books->first();
        
        $library->books = [
            $book,
            ['title' => 'Third Book'],
        ];
        $library->save();
        
        $this->assertCount(2, Models\Book::all());
        $this->assertDatabaseHas($book->getTable(), [
            'title' => $book->title,
        ]);
        $this->assertDatabaseHas($book->getTable(), [
            'title' => 'Third Book',
        ]);
    }
    
    public function testCanCascadeDeleteRecords()
    {
        $library = Models\Library::create([
            'name' => 'Central Library',
            'books' => [
                ['title' => 'First Book'], 
                ['title' => 'Second Book'], 
            ],
        ]);
        
        $library = $library->fresh();
        $library->delete();
        
        $this->assertCount(0, Models\Book::all());
    }
}