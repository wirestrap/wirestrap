<?php

namespace Tests;

use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase;
use Wirestrap\WirestrapServiceProvider;

abstract class UnitTestCase extends TestCase
{
    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return list<class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            WirestrapServiceProvider::class,
        ];
    }
}
