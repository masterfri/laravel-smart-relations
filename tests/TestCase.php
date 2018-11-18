<?php

namespace Masterfri\SmartRelations\Tests;

use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    public function setUp()
    {
        parent::setUp();
        $this->artisan('migrate', ['--database' => 'test']);
        $this->beforeApplicationDestroyed(function () {
            $this->artisan('migrate:rollback', ['--database' => 'test']);
        });
    }
    
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'test');
        
        //~ $app['config']->set('database.connections.test', [
            //~ 'driver' => 'sqlite',
            //~ 'database' => ':memory:',
            //~ 'prefix' => '',
        //~ ]);
        
        $app['config']->set('database.connections.test', [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'test',
            'username' => 'homestead',
            'password' => 'secret'
        ]);
    }
    
    protected function getPackageProviders($app)
    {
        return [
            TestServiceProvider::class
        ];
    }
}
