<?php

namespace Masterfri\SmartRelations\Tests;

class BelongsToManyCase extends TestCase
{
    public function testCanSetRelation()
    {
        $reader1 = Models\Reader::create(['name' => 'John']);
        $reader2 = Models\Reader::create(['name' => 'Jack']);
        $reader3 = Models\Reader::create(['name' => 'Jerry']);
        
        $library1 = Models\Library::create([
            'name' => 'Central Library',
            'readers' => [
                $reader1,
                $reader2,
            ],
        ]);
        
        $library2 = Models\Library::create([
            'name' => 'Other Library',
            'readers' => [
                $reader2->id,
                $reader3->id,
            ],
        ]);
        
        $reader1 = $reader1->fresh();
        $reader2 = $reader2->fresh();
        $reader3 = $reader3->fresh();
        
        $this->assertEquals(1, $reader1->subscriptions->count());
        $this->assertEquals(2, $reader2->subscriptions->count());
        $this->assertEquals(1, $reader3->subscriptions->count());
    }
    
    public function testCanCreateRelatedRecord()
    {
        $library = Models\Library::create([
            'name' => 'Central Library',
            'readers' => [
                ['name' => 'John'],
                ['name' => 'Jack'],
            ],
        ]);
        
        $this->assertCount(2, Models\Reader::all());
        $this->assertEquals(1, Models\Reader::first()->subscriptions->count());
    }
    
    public function testCanDetachRelatedRecords()
    {
        $reader1 = Models\Reader::create(['name' => 'John']);
        $reader2 = Models\Reader::create(['name' => 'Jack']);
        
        $library = Models\Library::create([
            'name' => 'Central Library',
            'readers' => [
                $reader1,
                $reader2,
            ],
        ]);
        
        $library->readers = [$reader1];
        $library->save();
        
        $reader2 = $reader2->fresh();
        $this->assertEquals(0, $reader2->subscriptions->count());
    }
    
    public function testCanClearPivotTableOnDelete()
    {
        $reader1 = Models\Reader::create(['name' => 'John']);
        $reader2 = Models\Reader::create(['name' => 'Jack']);
        
        $library = Models\Library::create([
            'name' => 'Central Library',
            'readers' => [
                $reader1,
                $reader2,
            ],
        ]);
        
        $reader1->delete();
        
        $library = $library->fresh();
        
        $this->assertEquals(1, $library->readers->count());
    }
}