# Laravel 5 Eloquent model routes

This package enables the developer to easily generate the routes available to a Eloquent Model.

## Contents

- [Installation](#installation)
- [Usage](#usage)
	- [Model](#usage_model)
	- [API Resource](#usage_resource)
	- [Only](#usage_only)
	- [Except](#usage_except)
	- [Builder](#usage_builder)
- [Testing](#testing)
- [Events](#events)
- [Issues](#issues)
- [License](#license)

<a name="installation" />

## Installation

## For Laravel ~5

    composer require collab-corp/laravel-model-routes

As with any package, it's a good idea to refresh composer autoloader.
```bash
composer dump-autoload
```

<a name="usage" />

## Usage

You may call instantiate or call the ```php CollabCorp\ModelRoutes ``` class anywhere,
but it is probably most useful in context of an Eloquent Model attribute accessor.

<a name="usage_model" />
### Eloquent Model Attribute Accessor
Wouldn't it be lovely to have your models return the route URLs it's relevant to?

```php 
use CollabCorp\LaravelModelRoutes\ModelRoutes;

class Model extends Eloquent 
{
	protected $appends = ['urls'];

	// ...

	public function getUrlsAttribute() 
	{
		return ModelRoutes::of($this);
	}
}
```
<a name="usage_resource" />
It can quickly become tedious to fill the only method, especially when you only want the conventional resource methods.
Conveniently, the resource method exists for exactly that purpose.
```php ModelRoutes::resource($model); ```

<a name="usage_only" />

Don't want all routes, just a select few?
```php ModelRoutes::of($model)->only(['index', 'show']); ```

<a name="usage_except" />

Want all routes, except a handful?
```php ModelRoutes::of($model)->except(['edit', 'create']); ```

<a name="usage_builder" />
The ModelRoutes forwards dynamic calls to the underlying ModelListBuilder, and conveniently returns the results.

Since the ```php ModelListBuilder ``` is Macroable, you may register your macros on it, those work just like regular methods.

<a name="testing" />
## Testing
```php
composer test
```

<a name="issues" />

## Issues 

If you discover any vulnerabilities, please e-mail them to me at jonas.kerwin.hansen@gmail.com.

For issues, open a issue on Github.

<a name="license" />

## License

laravel-model-routes is free software distributed under the terms of the MIT license.