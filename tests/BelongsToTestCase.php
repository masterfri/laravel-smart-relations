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
            'library' => $library,
        ]);
        
        $library = $library->fresh();
        
        $this->assertCount(2, $library->books);
    }
}