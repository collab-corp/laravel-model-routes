<?php

namespace Sasin91\LaravelModelRoutes\Tests;

use Illuminate\Container\Container;
use Illuminate\Contracts\Routing\UrlGenerator as UrlGeneratorContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Routing\RouteUrlGenerator;
use Illuminate\Routing\Router;
use Illuminate\Routing\UrlGenerator;
use PHPUnit\Framework\TestCase;
use Sasin91\LaravelModelRoutes\Tests\EventStub;
use Sasin91\LaravelModelRoutes\Tests\TestModel;

class ModelRoutesTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $container = new Container;

        $container->singleton(Router::class, function ($container) {
            return tap(new Router(new EventStub, $container), function ($router) {
                $router->get('test')->name('test.index');
                $router->get('test/{id}')->name('test.show');
                $router->post('test')->name('test.store');
                $router->delete('test/{id}')->name('test.delete');

                dd($router->getRoutes()->getRoutesByName());
            });
        });

        $container->make(Router::class);

        $container->bind(Request::class, function ($container) {
            return Request::createFromGlobals();
        });

        $container->bind(UrlGeneratorContract::class, function ($container) {
            return new UrlGenerator(
                $container->make(Router::class)->getRoutes(),
                $container->make(Request::class)
            );
        });

        $container->bind(RouteUrlGenerator::class, function ($container) {
            return new RouteUrlGenerator(
                $container->make(UrlGenerator::class),
                $container->make(Request::class)
            );
        });

        Container::setInstance(
            $container
        );
    }

    /** @test */
    function it_builds_the_available_named_route_urls_for_the_model() 
    {
        // dd((new TestModel)->urls);
    }  

    /** @test */
   function it_lists_the_available_indexes() 
   {
       
   }   

   /** @test */
   function it_lists_the_indexes_starting_with_first_request_segment() 
   {
       
   } 
}