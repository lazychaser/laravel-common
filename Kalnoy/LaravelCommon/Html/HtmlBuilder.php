<?php

namespace Kalnoy\LaravelCommon\Html;

use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Contracts\ArrayableInterface;

class HtmlBuilder extends \Illuminate\Html\HtmlBuilder {

    /**
     * The request.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    protected $session;

    protected static $alerts = [ 'success', 'danger', 'warning', 'info' ];

    public function __construct(UrlGenerator $url, Request $request, $session)
    {
        parent::__construct($url);

        $this->request = $request;
        $this->session = $session;
    }

    /**
     * Convert an array key name to the input name.
     * 
     * @param string $key
     * 
     * @return string
     */
    public function keyToName($key)
    {
        if (false === strpos($key, '.')) return $key;

        $key = explode('.', $key);

        return array_shift($key).'['.implode('][', $key).']';
    }

    /**
     * Append a class name to the options.
     *
     * @param array  $options
     * @param string $class
     *
     * @return void
     */
    public function appendClass(array &$options, $class)
    {
        $options['class'] = isset($options['class']) ? $options['class'].' '.$class : $class;
    }

    /**
     * Generate a breadcrumb markup.
     *
     * @param array $items
     * @param string $main
     * 
     * @return string
     */
    public function breadcrumb(array $items, $main = 'Main')
    {
        $html = '<li><a href="'.$this->url->to('/').'">'.$this->entities($main).'</a></li>';

        foreach ($items as $label => $url)
        {
            if (is_numeric($label))
            {
                $html .= '<li class="active">'.$this->entities($url).'</li>';
            }
            else
            {
                $html .= '<li><a href="'.$url.'">'.$this->entities($label).'</a>';
            }
        }

        return '<ol class="breadcrumb">'.$html.'</ol>';
    }

    /**
     * Generate a link to a compressed script if application is not in debug mode.
     *
     * @param string $url
     * @param array  $attributes
     * @param bool   $secure
     *
     * @return string
     */
    public function scriptVariant($url, $attributes = [], $secure = null)
    {
        if ( ! \Config::get('app.debug')) $url .= '.min';

        return $this->script($url . '.js', $attributes, $secure);
    }

    /**
     * Generate a ruble sign.
     *
     * @return string
     */
    public function rur()
    {
        return '<span class="currency currency-rur">&#8399;</span>';
    }

    /**
     * Render alert.
     *
     * @param string $message
     * @param string $status
     * @param bool   $dissmissable
     *
     * @return string
     */
    public function alert($message, $status = 'success', $dissmissable = true)
    {
        if ($dissmissable)
        {
            $status .= ' fade in';
            $message = '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' . $message;
        }

        return '<div class="alert alert-'.$status.'">'.$message.'</div>';
    }

    /**
     * Render error list.
     *
     * @param mixed $messages
     *
     * @return string
     */
    public function errors($messages)
    {
        if ($messages instanceof ArrayableInterface)
        {
            $messages = array_flatten($messages->toArray());
        }

        $html = '<ul class="errors">';

        foreach ($messages as $message)
        {
            $html .= '<li>'.$message.'</li>';
        }

        return $html.'</ul>';
    }

    /**
     * Render a list of alerts.
     *
     * @param string|null $domain
     *
     * @return string
     */
    public function alerts($domain = null)
    {
        $html = '';

        foreach (self::$alerts as $alert)
        {
            $key = $this->getAlertKey($alert, $domain);

            if ($value = $this->session->get($key))
            {
                $html .= $this->alert($value, $alert, true).PHP_EOL;
            }
        }

        return $html;
    }

    /**
     * Get whether session contains alerts of specified domain.
     *
     * @param string $domain
     *
     * @return bool
     */
    public function hasAlerts($domain = null)
    {
        foreach (self::$alerts as $key)
        {
            if ($this->session->get($this->getAlertKey($key, $domain))) return true;
        }

        return false;
    }

    /**
     * Get the key of the alert.
     *
     * @param string $alert
     * @param string $domain
     *
     * @return string
     */
    public function getAlertKey($alert, $domain)
    {
        return $domain ? $domain.'.'.$alert : $alert;
    }

    /**
     * Get an icon tag.
     * 
     * @return string
     */
    public function icon($name)
    {
        return '<span class="glyphicon glyphicon-'.$name.'"></span>';
    }

    public function model_states($model, $states)
    {
        $states = is_array($states) ? $states : array($states);

        $states = array_filter($states, function ($state) use ($model)
        {
            return $this->hasState($model, $state);
        });

        return implode($states, ' ');
    }

    public function hasState($model, $state)
    {
        $method = 'is'.\Str::camel($state);

        if (method_exists($model, $method)) return $model->{$method}();

        $state = 'is_'.str_replace('-', '_', $state);

        return $model->{$state};
    }
}