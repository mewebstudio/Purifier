# HTMLPurifier for Laravel 5

A simple [Laravel 5](http://www.laravel.com/) service provider for including the [HTMLPurifier for Laravel 5](https://github.com/mewebstudio/purifier).

for Laravel 4 [HTMLPurifier for Laravel 4](https://github.com/mewebstudio/Purifier/tree/master-l4)

This package can be installed via [Composer](http://getcomposer.org) by 
requiring the `mews/purifier` package in your project's `composer.json`:

```json
{
    "require": {
        "laravel/framework": "~5.0",
        "mews/purifier": "dev-master"
    }
}
```

Update your packages with `composer update` or install with `composer install`.

## Usage

To use the HTMLPurifier Service Provider, you must register the provider when bootstrapping your Laravel application. There are
essentially two ways to do this.

Find the `providers` key in `config/app.php` and register the HTMLPurifier Service Provider.

```php
    'providers' => [
        // ...
        'Mews\Purifier\PurifierServiceProvider',
    ]
```

Find the `aliases` key in `app/config/app.php`.

```php
    'aliases' => [
        // ...
        'Purifier' => 'Mews\Purifier\Facades\Purifier',
    ]
```

## Configuration

To use your own settings, publish config.

```$ php artisan vendor:publish```

Config file `config/purifier.php`

```php
return array(
    "settings" => array(
        "default" => array(
            "HTML.SafeIframe" => 'true',
            "URI.SafeIframeRegexp" => "%^(http://|https://|//)(www.youtube.com/embed/|player.vimeo.com/video/)%",
        ),
        "titles" => array(
            'AutoFormat.AutoParagraph' => false,
            'AutoFormat.Linkify' => false,
        )
    ),
);
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
