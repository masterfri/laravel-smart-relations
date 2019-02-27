<?php

namespace Masterfri\SmartRelations\Tests;

class MorphManyTestCase extends TestCase
{
    public function testCanCreateNewRecords()
    {
        $library = Models\Library::create([
            'name' => 'Central Library',
            'reviews' => [
                ['text' => 'First review'], 
                new Models\Review(['text' => 'Second review']),
            ],
        ]);
        
        $library = $library->fresh();
        
        $this->assertCount(2, $library->reviews);
    }
    
    public function testCanAttachExistingRecords()
    {
        $review1 = Models\Review::create([
            'text' => 'First review',
        ]);
        
        $review2 = Models\Review::create([
            'text' => 'Second review',
        ]);
        
        $review3 = Models\Review::create([
            'text' => 'Third review',
        ]);
        
        $library = Models\Library::create([
            'name' => 'Central Library',
            'reviews' => [
                $review1, 
                $review2->id,
                ['id' => $review3->id],
            ],
        ]);
        
        $library = $library->fresh();
        
        $this->assertCount(3, $library->reviews);
    }
    
    public function testCanUpdateRelatedRecords()
    {
        $reader = Models\Reader::create([
            'name' => 'John Doe',
        ]);
        
        $review = Models\Review::create([
            'text' => 'First review',
            'subject' => $reader,
        ]);
        
        $reader->reviews = [
            ['id' => $review->id, 'text' => 'New review'],
        ];
        $reader->save();
        
        $review = $review->fresh();
        
        $this->assertEquals('New review', $review->text);
    }
    
    public function testCanReplaceRelatedRecords()
    {
        $library = Models\Library::create([
            'name' => 'Central Library',
            'reviews' => [
                ['text' => 'First review'], 
                ['text' => 'Second review'], 
            ],
        ]);
        
        $library = $library->fresh();
        $review = $library->reviews->first();
        
        $library->reviews = [
            $review,
            ['text' => 'Third review'],
        ];
        $library->save();
        
        $this->assertCount(2, Models\Review::all());
        $this->assertDatabaseHas($review->getTable(), [
            'text' => $review->text,
        ]);
        $this->assertDatabaseHas($review->getTable(), [
            'text' => 'Third review',
        ]);
    }
    
    public function testCanCascadeDeleteRecords()
    {
        $reader = Models\Reader::create([
            'name' => 'John Doe',
            'reviews' => [
                ['text' => 'First review'], 
                ['text' => 'Second review'], 
            ],
        ]);
        
        $reader = $reader->fresh();
        $reader->delete();
        
        $this->assertCount(0, Models\Review::all());
    }
}