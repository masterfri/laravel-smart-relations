<?php

namespace Masterfri\SmartRelations\Tests;

class MorphOneTestCase extends TestCase
{
    public function testCanCreateNewRecords()
    {
        $reader = Models\Reader::create([
            'name' => 'John Doe',
            'rating' => [
                'score' => 5,
            ],
        ]);
        
        $reader = $reader->fresh();
        
        $this->assertNotNull($reader->rating);
    }
    
    public function testCanUnsetRelatedRecord()
    {
        $reader = Models\Reader::create([
            'name' => 'John Doe',
            'rating' => new Models\Rating([
                'score' => 5,
            ]),
        ]);
        
        $reader = $reader->fresh();
        
        $reader->rating = null;
        $reader->save();
        
        $this->assertCount(0, Models\Rating::all());
    }
    
    public function testCanReplaceRelatedRecord()
    {
        $library = Models\Library::create([
            'name' => 'Central library',
            'rating' => [
                'score' => 5,
            ],
        ]);
        
        $library = $library->fresh();
        $library->rating = [
            'score' => 10,
        ];
        $library->save();
        
        $this->assertCount(1, Models\Rating::all());
        $this->assertDatabaseHas($library->rating->getTable(), [
            'subject_id' => $library->id,
            'subject_type' => Models\Library::class,
        ]);
    }
    
    public function testCanCascadeDeleteRecords()
    {
        $library1 = Models\Library::create([
            'name' => 'Library 1',
            'rating' => [
                'score' => 5,
            ],
        ]);
        
        $library2 = Models\Library::create([
            'name' => 'Library 2',
            'rating' => [
                'score' => 10,
            ],
        ]);
        
        $library1->delete();
        
        $this->assertCount(1, Models\Rating::all());
        $this->assertDatabaseHas($library2->rating->getTable(), [
            'subject_id' => $library2->id,
            'subject_type' => Models\Library::class,
        ]);
    }
}