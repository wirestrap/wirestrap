<?php

namespace Wirestrap;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Wirestrap\Livewire\ModalManager;

class WirestrapServiceProvider extends ServiceProvider
{
    /**
     * Register package services.
     */
    #[\Override]
    public function register(): void
    {
        $this->mergeConfigFrom(
            path: __DIR__ . '/../config/wirestrap.php',
            key: 'wirestrap'
        );
    }

    /**
     * Bootstrap package.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(
            path: __DIR__ . '/../resources/views',
            namespace: 'wirestrap',
        );

        Blade::anonymousComponentPath(
            path: __DIR__ . '/../resources/views/components/form',
            prefix: 'wirestrap'
        );

        Blade::anonymousComponentPath(
            path: __DIR__ . '/../resources/views/components/ui',
            prefix: 'wirestrap'
        );

        Livewire::component(
            name: 'wirestrap.modal-manager',
            class: ModalManager::class
        );

        $this->publishes(
            paths: [__DIR__ . '/../config/wirestrap.php' => config_path('wirestrap.php')],
            groups: 'wirestrap:config'
        );

        $this->publishes(
            paths: [__DIR__ . '/../docs' => base_path('docs/wirestrap')],
            groups: 'wirestrap:docs'
        );
    }
}
