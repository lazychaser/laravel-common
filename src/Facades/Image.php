<?php

namespace Kalnoy\LaravelCommon\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Kalnoy\LaravelCommon\Images\ImagesProcessor
 */
class Image extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'images'; }
}