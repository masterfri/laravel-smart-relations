<?php

namespace Masterfri\SmartRelations\Tests;

use Masterfri\SmartRelations\RelationsManager;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Masterfri\SmartRelations\Processor\HasMany as HasManyOriginal;

class ExtendTestCase extends TestCase
{
    public function testImplementationCanBeExtended()
    {
        RelationsManager::extend(HasMany::class, Processor\Extended::class);
        
        $library = Models\Library::create([
            'name' => 'Central Library',
            'books' => [
                ['title' => 'First Book'], 
            ],
        ]);
        
        $library->books = [
            ['title' => 'Second Book'], 
        ];
        $library->save();
        
        RelationsManager::extend(HasMany::class, HasManyOriginal::class);
        
        $library = $library->fresh();
        
        $this->assertCount(2, $library->books);
    }
}