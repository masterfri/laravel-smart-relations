<?php

namespace Masterfri\SmartRelations\Tests;

class MorphToTestCase extends TestCase
{
    public function testCanSetRelation()
    {
        $library = Models\Library::create([
            'name' => 'Central Library',
        ]);
        
        $reader = Models\Reader::create([
            'name' => 'John',
        ]);
        
        Models\Review::create([
            'text' => 'Very rich library',
            'subject' => $library,
        ]);
        
        Models\Review::create([
            'text' => 'Always delays books',
            'subject' => $reader,
        ]);
        
        Models\Review::create([
            'text' => 'Returns books with missing pages',
            'subject' => [
                'id' => $reader->id,
                'type' => Models\Reader::class,
            ],
        ]);
        
        $library = $library->fresh();
        $reader = $reader->fresh();
        
        $this->assertCount(1, $library->reviews);
        $this->assertCount(2, $reader->reviews);
    }
    
    public function testCanUnsetRelation()
    {
        $library = Models\Library::create([
            'name' => 'Central Library',
        ]);
        
        $review = Models\Review::create([
            'text' => 'Very rich library',
            'subject' => $library,
        ]);
        
        $this->assertCount(1, $library->reviews);
        
        $review->subject = null;
        $review->save();
        
        $library = $library->fresh();
        
        $this->assertCount(0, $library->reviews);
    }
}