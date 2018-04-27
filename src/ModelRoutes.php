<?php

namespace Sasin91\LaravelModelRoutes;

use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Sasin91\LaravelModelRoutes\RouteListBuilder;

class ModelRoutes implements Jsonable, Arrayable
{
	/**
	 * Our route list builder.
	 * 
	 * @var \Sasin91\LaravelModelRoutes\RouteListBuilder
	 */
	protected $builder;

	/**
	 * Construct a new ModelRoutes instance.
	 * 
	 * @param \Illuminate\Database\Eloquent\Model $model 
	 */
	public function __construct(Model $model)
	{
		$this->builder = new RouteListBuilder($model);
	}

	/**
	 * Delegate dynamic calls to the underlying builder.
	 * 
	 * @param  string $method     
	 * @param  array $parameters 
	 * @return mixed             
	 */
	public function __call($method, $parameters)
	{
		$result = $this->builder->$method(...$parameters);

		// Allow the developer to get results from the builder directly.
		if (! $result instanceof RouteListBuilder) {
			return $result;
		}

		return $this;
	}

	/**
	 * Delegate dynamic properties to the underlying Collection proxies.
	 * 
	 * @param  string $key 
	 * @return mixed      
	 */
	public function __get($key)
	{
		return $this->builder->getResults()->$key;
	}

	/**
	 * Cast the model routes to a JSON string.
	 * 
	 * @return string
	 */
	public function __toString()
	{
		return $this->toJson();
	}

	/**
	 * Named constructor
	 * 
	 * @param  Model       $model     
	 * @param  string|null $routeName 
	 * 
	 * @return ModelRoutes
	 */
	public static function of(Model $model, string $routeName = null)
	{
		return tap(new static($model), function ($instance) use ($routeName) {
			if (filled($routeName)) {
				$instance->builder->routeName($routeName);
			}
		});
	}

	/**
	 * Get the resource routes of given model
	 * 
	 * @param  Model 	$model    
	 * @param  string 	$resource [The resource name or the methods to filter]
	 * 
	 * @return ModelRoutes         
	 */
	public static function resource(Model $model, string $resource = null)
	{
		return self::of($model, $resource)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
	}

	/**
     * Get the instance as an array.
     *
     * @return array
     */
	public function toArray()
	{
		return $this->builder->getResults()->toArray();
	}

	/**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
	public function toJson($options = 0)
	{
		return $this->builder->getResults()->toJson($options);
	}
}