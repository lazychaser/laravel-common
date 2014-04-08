<?php

namespace Kalnoy\LaravelCommon;

/**
 * The service provider.
 */
class ServiceProvider extends \Illuminate\Support\ServiceProvider {

    public function register()
    {
        
    }

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