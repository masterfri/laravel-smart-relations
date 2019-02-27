<?php

namespace Masterfri\SmartRelations\Tests;

class BelongsToTestCase extends TestCase
{
    public function testCanSetRelation()
    {
        $library = Models\Library::create([
            'name' => 'Central Library',
        ]);
        
        Models\Book::create([
            'title' => 'First book',
            'library' => $library,
        ]);
        
        Models\Book::create([
            'title' => 'Second book',
            'library' => $library->id,
        ]);
        
        $library = $library->fresh();
        
        $this->assertCount(2, $library->books);
    }
    
    public function testCanUnsetRelation()
    {
        $library = Models\Library::create([
            'name' => 'Central Library',
        ]);
        
        $book = Models\Book::create([
            'title' => 'First book',
            'library' => $library,
        ]);
        
        $this->assertCount(1, $library->books);
        
        $book->library = '';
        $book->save();
        
        $library = $library->fresh();
        
        $this->assertCount(0, $library->books);
    }
}