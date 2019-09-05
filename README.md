# HTMLPurifier for Laravel 5 & 6

[![Build Status](https://scrutinizer-ci.com/g/mewebstudio/Purifier/badges/build.png?b=master)](https://scrutinizer-ci.com/g/mewebstudio/Purifier/build-status/master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mewebstudio/Purifier/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mewebstudio/Purifier/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/mews/Purifier/v/stable.svg)](https://packagist.org/packages/mews/Purifier)
[![Latest Unstable Version](https://poser.pugx.org/mews/Purifier/v/unstable.svg)](https://packagist.org/packages/mews/Purifier)
[![License](https://poser.pugx.org/mews/Purifier/license.svg)](https://packagist.org/packages/mews/Purifier)
[![Total Downloads](https://poser.pugx.org/mews/Purifier/downloads.svg)](https://packagist.org/packages/mews/Purifier)

A simple [Laravel 5 & 6](http://www.laravel.com/) service provider for including the [HTMLPurifier for Laravel](https://github.com/mewebstudio/purifier).

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

# For Laravel 5.0 to 5.4:
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

# For Laravel 5.5+: 
The service provider will be auto-discovered. You do not need to add the provider anywhere. 

## Configuration

To use your own settings, publish config.

```$ php artisan vendor:publish --provider="Mews\Purifier\PurifierServiceProvider"```

Config file `config/purifier.php` should like this

```php

return [
    'encoding'      => 'UTF-8',
    'finalize'      => true,
    'cachePath'     => storage_path('app/purifier'),
    'cacheFileMode' => 0755,
    'settings'      => [
        'default' => [
            'HTML.Doctype'             => 'HTML 4.01 Transitional',
            'HTML.Allowed'             => 'div,b,strong,i,em,u,a[href|title],ul,ol,li,p[style],br,span[style],img[width|height|alt|src]',
            'CSS.AllowedProperties'    => 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align',
            'AutoFormat.AutoParagraph' => true,
            'AutoFormat.RemoveEmpty'   => true,
        ],
        'test'    => [
            'Attr.EnableID' => 'true',
        ],
        "youtube" => [
            "HTML.SafeIframe"      => 'true',
            "URI.SafeIframeRegexp" => "%^(http://|https://|//)(www.youtube.com/embed/|player.vimeo.com/video/)%",
        ],
        'custom_definition' => [
            'id'  => 'html5-definitions',
            'rev' => 1,
            'debug' => false,
            'elements' => [
                // http://developers.whatwg.org/sections.html
                ['section', 'Block', 'Flow', 'Common'],
                ['nav',     'Block', 'Flow', 'Common'],
                ['article', 'Block', 'Flow', 'Common'],
                ['aside',   'Block', 'Flow', 'Common'],
                ['header',  'Block', 'Flow', 'Common'],
                ['footer',  'Block', 'Flow', 'Common'],
				
				// Content model actually excludes several tags, not modelled here
                ['address', 'Block', 'Flow', 'Common'],
                ['hgroup', 'Block', 'Required: h1 | h2 | h3 | h4 | h5 | h6', 'Common'],
				
				// http://developers.whatwg.org/grouping-content.html
                ['figure', 'Block', 'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common'],
                ['figcaption', 'Inline', 'Flow', 'Common'],
				
				// http://developers.whatwg.org/the-video-element.html#the-video-element
                ['video', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', [
                    'src' => 'URI',
					'type' => 'Text',
					'width' => 'Length',
					'height' => 'Length',
					'poster' => 'URI',
					'preload' => 'Enum#auto,metadata,none',
					'controls' => 'Bool',
                ]],
                ['source', 'Block', 'Flow', 'Common', [
					'src' => 'URI',
					'type' => 'Text',
                ]],

				// http://developers.whatwg.org/text-level-semantics.html
                ['s',    'Inline', 'Inline', 'Common'],
                ['var',  'Inline', 'Inline', 'Common'],
                ['sub',  'Inline', 'Inline', 'Common'],
                ['sup',  'Inline', 'Inline', 'Common'],
                ['mark', 'Inline', 'Inline', 'Common'],
                ['wbr',  'Inline', 'Empty', 'Core'],
				
				// http://developers.whatwg.org/edits.html
                ['ins', 'Block', 'Flow', 'Common', ['cite' => 'URI', 'datetime' => 'CDATA']],
                ['del', 'Block', 'Flow', 'Common', ['cite' => 'URI', 'datetime' => 'CDATA']],
            ],
            'attributes' => [
                ['iframe', 'allowfullscreen', 'Bool'],
                ['table', 'height', 'Text'],
                ['td', 'border', 'Text'],
                ['th', 'border', 'Text'],
                ['tr', 'width', 'Text'],
                ['tr', 'height', 'Text'],
                ['tr', 'border', 'Text'],
            ],
        ],
        'custom_attributes' => [
            ['a', 'target', 'Enum#_blank,_self,_target,_top'],
        ],
        'custom_elements' => [
            ['u', 'Inline', 'Inline', 'Common'],
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
