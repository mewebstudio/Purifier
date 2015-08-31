# HTMLPurifier for Laravel 5

[![Build Status](https://scrutinizer-ci.com/g/mewebstudio/Purifier/badges/build.png?b=master)](https://scrutinizer-ci.com/g/mewebstudio/Purifier/build-status/master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mewebstudio/Purifier/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mewebstudio/Purifier/?branch=master)

A simple [Laravel 5](http://www.laravel.com/) service provider for including the [HTMLPurifier for Laravel 5](https://github.com/mewebstudio/purifier).

for Laravel 4 [HTMLPurifier for Laravel 4](https://github.com/mewebstudio/Purifier/tree/master-l4)

This package can be installed via [Composer](http://getcomposer.org) by 
requiring the `mews/purifier` package in your project's `composer.json`:

```json
{
    "require": {
        "laravel/framework": "~5.0",
        "mews/purifier": "~2.0",
    }
}
```

or

Require this package with composer:
```
composer require mews/purifier
```

Update your packages with `composer update` or install with `composer install`.

## Usage

To use the HTMLPurifier Service Provider, you must register the provider when bootstrapping your Laravel application. There are
essentially two ways to do this.

Find the `providers` key in `config/app.php` and register the HTMLPurifier Service Provider.

```php
    'providers' => [
        // ...
        Mews\Purifier\PurifierServiceProvider::class,
    ]
```

Find the `aliases` key in `app/config/app.php`.

```php
    'aliases' => [
        // ...
        'Purifier' => Mews\Purifier\Facades\Purifier::class,
    ]
```

## Configuration

To use your own settings, publish config.

```$ php artisan vendor:publish```

Config file `config/purifier.php` should like this

```php

return [

    'encoding' => 'UTF-8',
    'finalize' => true,
    'preload'  => false,
    'cachePath' => null,
    'settings' => [
        'default' => [
            'HTML.Doctype'             => 'XHTML 1.0 Strict',
            'HTML.Allowed'             => 'div,b,strong,i,em,a[href|title],ul,ol,li,p[style],br,span[style],img[width|height|alt|src]',
            'CSS.AllowedProperties'    => 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align',
            'AutoFormat.AutoParagraph' => true,
            'AutoFormat.RemoveEmpty'   => true
        ],
        'test' => [
            'Attr.EnableID' => true
        ],
        "youtube" => [
            "HTML.SafeIframe" => 'true',
            "URI.SafeIframeRegexp" => "%^(http://|https://|//)(www.youtube.com/embed/|player.vimeo.com/video/)%",
        ],
    ],

];
```


## Example

default
```php
clean(Input::get('inputname'));
```
or

```php
Purifier::clean(Input::get('inputname'));
```

dynamic config
```php
clean('This is my H1 title', 'titles');
clean('This is my H1 title', array('Attr.EnableID' => true));
```
or

```php
Purifier::clean('This is my H1 title', 'titles');
Purifier::clean('This is my H1 title', array('Attr.EnableID' => true));
```

for Laravel 4 [HTMLPurifier for Laravel 4](https://github.com/mewebstudio/Purifier/tree/master-l4)
