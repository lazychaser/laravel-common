<?php

namespace Kalnoy\LaravelCommon\Images;

use Illuminate\Support\ServiceProvider;
use Intervention\Image\Image;

/**
 * Images service provider.
 */
class ImagesServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    protected $defer = true;

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->app->singleton('images', function ($app) {
            $path = config('filesystems.images', 'image_cache');

            return new ImageProcessor($app['image'], $app['files'], $path);
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