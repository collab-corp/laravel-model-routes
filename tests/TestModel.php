<?php

namespace Sasin91\LaravelModelRoutes\Tests;

use Illuminate\Database\Eloquent\Model;
use Sasin91\LaravelModelRoutes\ModelRoutes;

class TestModel extends Model
{
	public $exists = true;

	protected $guarded = [];

	protected $appends = ['urls'];

	public function routeName()
	{
		return 'test';
	}

	public function setRouteKeyName($value)
	{
		$this->primaryKey = $value;

		return $this;
	}

	public function getUrlsAttribute()
	{
		return ModelRoutes::of($this)->toArray();
	}
}