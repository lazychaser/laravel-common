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

    public function __construct(UrlGenerator $url, Request $request, $session)
    {
        parent::__construct($url);

        $this->request = $request;
        $this->session = $session;
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
        $message = $this->entities($message);

        if ($dissmissable)
        {
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
     * @param string|null $prefix
     *
     * @return string
     */
    public function alerts($prefix = null)
    {
        $html = '';

        foreach ([ 'success', 'danger', 'warning', 'info' ] as $alert)
        {
            $key = $prefix ? $prefix.$alert : $alert;

            if ($value = $this->session->get($key))
            {
                $html .= $this->alert($value, $alert, true).PHP_EOL;
            }
        }

        return $html;
    }
}