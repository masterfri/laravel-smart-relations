<?php

namespace Masterfri\SmartRelations\Tests;

class MorphToManyCase extends TestCase
{
    public function testCanSetRelation()
    {
        $tag1 = Models\Tag::create(['tag' => 'good']);
        $tag2 = Models\Tag::create(['tag' => 'bad']);
        $tag3 = Models\Tag::create(['tag' => 'fine']);
        
        $library = Models\Library::create([
            'name' => 'Central Library',
            'tags' => [
                $tag1,
                $tag2->id,
            ],
        ]);
        
        $reader = Models\Reader::create([
            'name' => 'John Doe',
            'tags' => [
                $tag2,
                $tag3->id,
            ],
        ]);
        
        $library = $library->fresh();
        $reader = $reader->fresh();
        
        $this->assertEquals(2, $library->tags->count());
        $this->assertEquals(2, $reader->tags->count());
    }
    
    public function testCanCreateRelatedRecord()
    {
        $library = Models\Library::create([
            'name' => 'Central Library',
            'tags' => [
                ['tag' => 'good'],
                ['tag' => 'bad'],
            ],
        ]);
        
        $library = $library->fresh();
        
        $this->assertCount(2, Models\Tag::all());
        $this->assertEquals(2, $library->tags->count());
    }
    
    public function testCanDetachRelatedRecords()
    {
        $tag1 = Models\Tag::create(['tag' => 'good']);
        $tag2 = Models\Tag::create(['tag' => 'bad']);
        
        $library = Models\Library::create([
            'name' => 'Central Library',
            'tags' => [
                $tag1,
                $tag2,
            ],
        ]);
        
        $library = $library->fresh();
        $library->tags = [$tag1];
        $library->save();
        
        $tag2 = $tag2->fresh();
        $this->assertEquals(0, $tag2->libraries->count());
    }
    
    public function testCanClearPivotTableOnDelete()
    {
        $tag1 = Models\Tag::create(['tag' => 'good']);
        $tag2 = Models\Tag::create(['tag' => 'bad']);
        
        $library = Models\Library::create([
            'name' => 'Central Library',
            'tags' => [
                $tag1,
                $tag2,
            ],
        ]);
        
        $reader = Models\Reader::create([
            'name' => 'John Doe',
            'tags' => [
                $tag1,
                $tag2,
            ],
        ]);
        
        $reader->delete();
        $tag1->delete();
        
        $this->assertDatabaseMissing('taggables', [
            'tag_id' => $tag1->id,
        ]);
        $this->assertDatabaseMissing('taggables', [
            'taggable_id' => $reader->id,
            'taggable_type' => Models\Reader::class,
        ]);
    }
}