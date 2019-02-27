<?php

namespace Masterfri\SmartRelations\Tests;

use Illuminate\Database\Eloquent\Relations\Relation;

class MorphMapTestCase extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        
        Relation::morphMap([
            'library' => Models\Library::class,
            'reader' => Models\Reader::class,
        ], false);
    }
    
    public function tearDown()
    {
        parent::tearDown();
        
        Relation::morphMap([
            'nothing' => 'nothing',
        ], false);
    }
    
    public function testHasMany()
    {
        $review = new Models\Review(['text' => 'Some review']);
        $library = Models\Library::create([
            'name' => 'Central Library',
            'reviews' => [
                $review,
            ],
        ]);
        
        $library = $library->fresh();
        
        $this->assertCount(1, $library->reviews);
        $this->assertDatabaseHas($review->getTable(), [
            'subject_id' => $library->id,
            'subject_type' => 'library',
        ]);
    }
    
    public function testHasOne()
    {
        $rating = new Models\Rating(['score' => 5]);
        $reader = Models\Reader::create([
            'name' => 'John Doe',
            'rating' => $rating,
        ]);
        
        $reader = $reader->fresh();
        
        $this->assertNotNull($reader->rating);
        $this->assertDatabaseHas($rating->getTable(), [
            'subject_id' => $reader->id,
            'subject_type' => 'reader',
        ]);
    }
    
    public function testBelongsTo()
    {
        $reader = Models\Reader::create([
            'name' => 'John Doe',
        ]);
        $library = Models\Library::create([
            'name' => 'Central Library',
        ]);
        
        $review1 = Models\Review::create([
            'text' => 'Some review',
            'subject' => [
                'id' => $reader->id,
                'type' => 'reader',
            ],
        ]);
        $review2 = Models\Review::create([
            'text' => 'Some review',
            'subject' => [
                'id' => $library->id,
                'type' => 'library',
            ],
        ]);
        
        $reader = $reader->fresh();
        $library = $library->fresh();
        
        $this->assertCount(1, $reader->reviews);
        $this->assertCount(1, $library->reviews);
    }
    
    public function testBelongsToMany()
    {
        $tag = Models\Tag::create(['tag' => 'good']);
        
        $library = Models\Library::create([
            'name' => 'Central Library',
            'tags' => [
                $tag,
            ],
        ]);
        
        $reader = Models\Reader::create([
            'name' => 'John Doe',
            'tags' => [
                $tag,
            ],
        ]);
        
        $this->assertDatabaseHas('taggables', [
            'tag_id' => $tag->id,
            'taggable_id' => $library->id,
            'taggable_type' => 'library',
        ]);
        $this->assertDatabaseHas('taggables', [
            'tag_id' => $tag->id,
            'taggable_id' => $reader->id,
            'taggable_type' => 'reader',
        ]);
    }
}