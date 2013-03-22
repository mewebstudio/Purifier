<?php namespace Mews\Purifier\Facades;

use Illuminate\Support\Facades\Facade;

class Purifier extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'purifier'; }

}