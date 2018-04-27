<?php

namespace Sasin91\LaravelModelRoutes;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\RouteUrlGenerator;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;

class RouteListBuilder
{
	use Macroable;

	/**
	 * Name of the model routes
	 * 
	 * @var string
	 */
	protected $routeName;

	/**
	 * Collection of model routes
	 * 
	 * @var \Illuminate\Support\Collection
	 */
	protected $routes;

	/**
	 * Laravel Router
	 * 
	 * @var \Illuminate\Routing\Router
	 */
	protected $router;

	/**
	 * Laravel route url generator
	 * 
	 * @var \Illuminate\Routing\RouteUrlGenerator
	 */
	protected $urlGenerator;

	/**
	 * The eloquent model.
	 * 
	 * @var \Illuminate\Database\Eloquent\Model
	 */
	protected $model;

	/**
	 * Construct a new Route list builder instance
	 * 
	 * @param Model $model 
	 */
	public function __construct(Model $model)
	{
		$this->model = $model;
		$this->routeName = $this->modelRouteName($model);
		
		$this->whereModel($model);
	}

	/**
	 * The the route name.
	 * 
	 * @param  string $name 
	 * @return $this
	 */
	public function routeName(string $name)
	{
		$this->routeName = $name;

		return $this;
	}

	/**
	 * Scope the routes by given model
	 * 
	 * @param  Model  $model 
	 * @return $this        
	 */
	public function whereModel(Model $model)
	{
		$this->routes = $this->routes()->filter(function ($route, $name) {
			return starts_with($name, $this->routeName);
		});

		return $this;
	}

	/**
	 * Pick only the given methods
	 * 
	 * @param  ...string[] $methods 
	 * @return $this          
	 */
	public function only(...$methods)
	{
		$methods = array_wrap(...$methods);

		$this->routes = $this->routes()->only(
			$this->qualifyRouteMethods($methods)
		);

		return $this;
	}

	/**
	 * Alias for getResults.
	 * 
	 * @return \Illuminate\Support\Collection
	 */
	public function get()
	{
		return $this->getResults();
	}

	/**
	 * Get the collection of route urls
	 * 
	 * @return \Illuminate\Support\Collection
	 */
	public function getResults()
	{
		return $this->routes()->map(function ($route) {
			return $this->url()->to(
				$route, 
				[$this->model->getRouteKeyName() => $this->model->getRouteKey()]
			);
		});
	}

	/**
	 * Get the route collection.
	 * 
	 * @return \Illuminate\Support\Collection
	 */
	public function routes()
	{
		if ($this->routes) {
			return $this->routes;
		}

		return $this->routes = $this->nameKeyedRoutes();
	}

	/**
	 * Get all routes keyed by name
	 * 
	 * @return \Illuminate\Support\Collection
	 */
	protected function nameKeyedRoutes()
	{
		return collect($this->router()->getRoutes()->getRoutesByName());
	}

	/**
	 * Resolve the Laravel route URL generator
	 * 
	 * @return \Illuminate\Routing\RouteUrlGenerator
	 */
	protected function url()
	{
		if ($this->urlGenerator) {
			return $this->urlGenerator;
		}

		return $this->urlGenerator = Container::getInstance()->make(RouteUrlGenerator::class);
	}

	/**
	 * Resolve the Laravel Router.
	 * 
	 * @return \Illuminate\Routing\Router
	 */
	protected function router()
	{
		if ($this->router) {
			return $this->router;
		}

		return $this->router = Container::getInstance()->make(Router::class);
	}

	/**
	 * Get the model route name
	 * 
	 * @param  Model  $model 
	 * @return string
	 */
	protected function modelRouteName(Model $model)
	{
		if (method_exists($model, 'routeName')) {
			return $model->routeName();
		}

		$class = Str::lower(class_basename($model));

		return Str::plural($class);
	}

	/**
	 * Qualify given route methods
	 * 
	 * @param  array  $methods 
	 * @return array
	 */
	protected function qualifyRouteMethods(array $methods)
	{
		return array_map(function ($method) {
			return "{$this->routeName}.{$method}";
		}, $methods);
	}
}