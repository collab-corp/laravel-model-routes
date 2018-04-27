<?php

namespace CollabCorp\LaravelModelRoutes\Tests;

use Illuminate\Container\Container;
use Illuminate\Contracts\Routing\UrlGenerator as UrlGeneratorContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Routing\RouteUrlGenerator;
use Illuminate\Routing\Router;
use Illuminate\Routing\UrlGenerator;
use PHPUnit\Framework\TestCase;
use CollabCorp\LaravelModelRoutes\Tests\EventStub;
use CollabCorp\LaravelModelRoutes\Tests\TestModel;

class ModelRoutesTest extends TestCase
{
    protected $app;

    protected function setUp()
    {
        parent::setUp();

        $this->app = new Container;

        $this->app->singleton(Router::class, function ($app) {
            $router = new Router(new EventStub, $app);

            $router->get('test')->name('test.index');
            $router->get('test/{id}')->name('test.show');
            $router->post('test')->name('test.store');
            $router->match(['PUT', 'PATCH'], 'test/{id}')->name('test.update');
            $router->delete('test/{id}')->name('test.delete');

            $router->getRoutes()->refreshNameLookups();

            return $router;
        });

        $this->app->singleton(Request::class, function () {
            return Request::createFromGlobals();
        });

        $this->app->bind(UrlGeneratorContract::class, function ($app) {
            return new UrlGenerator(
                $app->make(Router::class)->getRoutes(),
                $app->make(Request::class)
            );
        });

        $this->app->bind(RouteUrlGenerator::class, function ($app) {
            return new RouteUrlGenerator(
                $app->make(UrlGenerator::class),
                $app->make(Request::class)
            );
        });

        Container::setInstance(
            $this->app
        );
    }

    /** @test */
    function it_builds_the_available_named_route_urls_for_the_model() 
    {
        $this->assertEquals([
            'test.index' => '/test#', 
            'test.show' => '/test/1#',
            'test.store' => '/test#', 
            'test.update' => '/test/1#',
            'test.delete' => '/test/1#'
        ], ((new TestModel(['id' => 1]))->urls));
    }  

    /** @test */
   function it_lists_the_available_indexes() 
   {
       $this->markTestIncomplete();
   }   

   /** @test */
   function it_lists_the_indexes_starting_with_first_request_segment() 
   {
       $this->markTestIncomplete();
   } 
}