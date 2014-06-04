<?php

namespace Kalnoy\LaravelCommon;

/**
 * The service provider.
 */
class ServiceProvider extends \Illuminate\Support\ServiceProvider {

    public function register()
    {
        $this->registerDispatcher();
    }

    /**
     * Register an events dispatcher.
     */
    protected function registerDispatcher()
    {
        $this->app->bindShared('Kalnoy\LaravelCommon\Events\Dispatcher', function ($app)
        {
            return new Events\Dispatcher($app['events']);
        });
    }

    /**
     * Boot a service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->extendValidator();

        Mail\Notifier::setMailer($this->app['mailer']);

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