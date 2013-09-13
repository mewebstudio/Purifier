# HTMLPurifier for Laravel 4

A simple [Laravel 4](http://four.laravel.com/) service provider for including the [HTMLPurifier for Laravel 4](https://github.com/mewebstudio/purifier).

## Installation

The HTMLPurifier Service Provider can be installed via [Composer](http://getcomposer.org) by requiring the
`mews/purifier` package and setting the `minimum-stability` to `dev` (required for Laravel 4) in your
project's `composer.json`.

```json
{
    "require": {
        "laravel/framework": "4.0.*",
        "mews/purifier": "dev-master"
    },
    "minimum-stability": "dev"
}
```

Update your packages with ```composer update``` or install with ```composer install```.

## Usage

To use the HTMLPurifier Service Provider, you must register the provider when bootstrapping your Laravel application. There are
essentially two ways to do this.

Find the `providers` key in `app/config/app.php` and register the HTMLPurifier Service Provider.

```php
    'providers' => array(
        // ...
        'Mews\Purifier\PurifierServiceProvider',
    )
```

Find the `aliases` key in `app/config/app.php`.

```php
    'aliases' => array(
        // ...
        'Purifier' => 'Mews\Purifier\Facades\Purifier',
    )
```

## Example

```php

    Purifier::clean(Input::get('inputname'));

```

## Configuration
To configure Purifier directly in Laravel create the file `app/config/purifier.php` and add your settings.
```php
return array(
    "settings" => array(
        "HTML.SafeIframe" => 'true',
        "URI.SafeIframeRegexp" => "%^(http://|https://|//)(www.youtube.com/embed/|player.vimeo.com/video/)%",
    ),
);
```
                      

## Links

* [HTMLPurifier Library website](http://htmlpurifier.org/)

* [L4 HTMLPurifier on Github](https://github.com/mewebstudio/purifier)
* [L4 HTMLPurifier on Packagist](https://packagist.org/packages/mews/purifier)
* [License](http://www.gnu.org/licenses/lgpl-2.1.html)
* [Laravel website](http://laravel.com)
* [Laravel Turkiye website](http://www.laravel.gen.tr)
* [MeWebStudio website](http://www.mewebstudio.com)
