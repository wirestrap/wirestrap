<?php

namespace Tests;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\View;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase;
use Pest\Browser\Browsable;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Wirestrap\WirestrapServiceProvider;

abstract class BrowserTestCase extends TestCase
{
    use Browsable {
        visit as protected browsableVisit;
    }

    protected mixed $_lastPage = null;

    /**
     * Wraps Browsable::visit() to capture the page reference for tearDown.
     */
    public function visit(array|string $url, array $options = []): mixed
    {
        $this->_lastPage = $this->browsableVisit($url, $options);

        return $this->_lastPage;
    }

    /**
     * Asserts no JS errors or console logs after every test.
     */
    protected function tearDown(): void
    {
        $this->_lastPage?->assertNoSmoke();
        $this->_lastPage = null;

        parent::tearDown();
    }

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

    /**
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:Hupx3yAySikrM2/edkZQNQHslgDWYfiBfCuSThJ5SK8=');
        $app['config']->set('view.paths', [__DIR__ . '/Browser/views']);

        Blade::anonymousComponentPath(__DIR__ . '/Browser/views/components');

        $app['config']->set('livewire.inject_assets', false);
        $app['config']->set('livewire.class_namespace', 'Tests\\Browser\\Livewire');

        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);
    }

    /**
     * @param  \Illuminate\Routing\Router  $router
     */
    protected function defineWebRoutes($router): void
    {
        $router->get('_ws/wirestrap.css', function (): BinaryFileResponse {
            $path = realpath(__DIR__ . '/Browser/dist/wirestrap.css');

            if (!$path || !file_exists($path)) {
                abort(500, 'WireStrap test CSS not built. Run: npm run build:test');
            }

            return response()->file($path, [
                'Content-Type' => 'text/css',
            ]);
        });

        $router->get('_ws/wirestrap.js', function (): BinaryFileResponse {
            $path = realpath(__DIR__ . '/Browser/dist/wirestrap.js');

            if (!$path || !file_exists($path)) {
                abort(500, 'WireStrap test JS not built. Run: npm run build:test');
            }

            return response()->file($path, [
                'Content-Type' => 'application/javascript',
            ]);
        });

        // Bootstrap Icons webfont, referenced by the test stylesheet. Served from
        // node_modules so the icons are never copied into the package.
        $router->get('_ws/fonts/{file}', function (string $file): Response {
            $path = realpath(__DIR__ . '/../node_modules/bootstrap-icons/font/fonts/' . basename($file));

            if (!$path || !file_exists($path)) {
                abort(500, 'Bootstrap Icons font not found. Run: npm install');
            }

            return response((string) file_get_contents($path), 200, [
                'Content-Type' => str_ends_with($path, '.woff2') ? 'font/woff2' : 'font/woff',
            ]);
        })->where('file', '[A-Za-z0-9._-]+');

        $router->get('_ws/test/{page}', fn(string $page): View => view('pages.' . str_replace('/', '.', $page)))
            ->where('page', '.*')
            ->middleware('web');
    }
}
