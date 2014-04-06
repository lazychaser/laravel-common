<?php

namespace Kalnoy\LaravelCommon;

/**
 * The service provider.
 */
class ServiceProvider extends \Illuminate\Support\ServiceProvider {

    /**
     * Boot a service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->extendValidator();

        include __DIR__ . '/../../functions.php';
    }

    /**
     * Register a service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerHtmlBuilder();
    }

    /**
     * Register custom html builder.
     *
     * @return void
     */
    public function registerHtmlBuilder()
    {
        $this->app->bindShared('html', function ($app)
        {
            return new Html\HtmlBuilder($app['url'], $app['request']);
        });
    }

    /**
     * Add some validation rules.
     *
     * @return void
     */
    protected function extendValidator()
    {
        $validator = $this->app['validator'];

        $rules = [ 'slug' ];

        foreach ($rules as $rule)
        {
            $validator->extend($rule, 'Kalnoy\LaravelCommon\ValidationRules@' . $rule);
        }
    }

}