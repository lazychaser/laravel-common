<?php

namespace Kalnoy\LaravelCommon\Images;

use Illuminate\Support\ServiceProvider;
use Intervention\Image\Image;

/**
 * Images service provider.
 */
class ImagesServiceProvider extends ServiceProvider {

    /**
     * {@inheritdoc}
     */
    protected $defer = true;

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->app->singleton('images', function ($app)
        {
            return new ImageProcessor($app['image'], $app['files'], config('filesystems.images', 'image_cache'));
        });
    }

    /**
     * {@inheritdoc}
     */
    public function provides()
    {
        return [ 'images' ];
    }

}