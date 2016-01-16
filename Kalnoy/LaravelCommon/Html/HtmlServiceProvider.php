<?php

namespace Kalnoy\LaravelCommon\Html;

class HtmlServiceProvider extends \Collective\Html\HtmlServiceProvider {

    /**
     * Register the HTML builder instance.
     *
     * @return void
     */
    protected function registerHtmlBuilder()
    {
        $this->app->singleton('html', function ($app)
        {
            return new HtmlBuilder($app['url'], $app['session.store']);
        });

        $this->app->alias('html', 'Kalnoy\LaravelCommon\Html\HtmlBuilder');
    }

    /**
     * Register the form builder instance.
     *
     * @return void
     */
    protected function registerFormBuilder()
    {
        $this->app->singleton('form', function ($app)
        {
            $form = new FormBuilder($app['html'], $app['url'], $app['view'], $app['session.store']->getToken());

            return $form->setSessionStore($app['session.store']);
        });

        $this->app->alias('form', 'Kalnoy\LaravelCommon\Html\FormBuilder');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array_merge(parent::provides(), [

            'Kalnoy\LaravelCommon\Html\HtmlBuilder',
            'Kalnoy\LaravelCommon\Html\FormBuilder',
        ]);
    }

}