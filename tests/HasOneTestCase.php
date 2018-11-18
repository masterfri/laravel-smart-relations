<?php

namespace Masterfri\SmartRelations\Tests;

class HasOneTestCase extends TestCase
{
    public function testCanCreateNewRecords()
    {
        $book = Models\Book::create([
            'title' => 'First book',
            'card' => [],
        ]);
        
        $book = $book->fresh();
        
        $this->assertNotNull($book->card);
    }
    
    public function testCanUnsetRelatedRecord()
    {
        $book = Models\Book::create([
            'title' => 'First book',
            'card' => new Models\BookCard(),
        ]);
        
        $book = $book->fresh();
        
        $book->card = null;
        $book->save();
        
        $this->assertCount(0, Models\BookCard::all());
    }
    
    public function testCanReplaceRelatedRecord()
    {
        $book = Models\Book::create([
            'title' => 'First book',
            'card' => new Models\BookCard(),
        ]);
        
        $book = $book->fresh();
        $reader = Models\Reader::create([
            'name' => 'John',
        ]);
        
        $book->card = [
            'reader' => $reader,
        ];
        $book->save();
        
        $this->assertCount(1, Models\BookCard::all());
        $this->assertDatabaseHas($book->card->getTable(), [
            'reader_id' => $reader->id,
        ]);
    }
    
    public function testCanCascadeDeleteRecords()
    {
        $book1 = Models\Book::create([
            'title' => 'First book',
            'card' => new Models\BookCard(),
        ]);
        
        $book2 = Models\Book::create([
            'title' => 'Second book',
            'card' => new Models\BookCard(),
        ]);
        
        $book1->delete();
        
        $this->assertCount(1, Models\BookCard::all());
        $this->assertDatabaseHas($book2->card->getTable(), [
            'book_id' => $book2->id,
        ]);
    }
}