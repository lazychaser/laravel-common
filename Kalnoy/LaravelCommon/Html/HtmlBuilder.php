<?php

namespace Kalnoy\LaravelCommon\Html;

use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ArrayableInterface;

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
     * Generate a breadcrumb markup.
     *
     * @param array $items
     * @param string $main
     * 
     * @return string
     */
    public function breadcrumb(array $items, $main = 'Main')
    {
        $html = PHP_EOL . $this->menuItem($main, $this->url->to('/'));

        foreach ($items as $label => $url)
        {
            if (is_numeric($label))
            {
                $label = $url;
                $url = null;
            }

            $html .= $this->menuItem($label, $url);
        }

        return '<ol class="breadcrumb">' . $html . '</ol>';
    }

    /**
     * Generate a list of menu items.
     *
     * @param array $items
     *
     * @return string
     */
    public function menuItems(array $items)
    {
        $html = '';

        foreach ($items as $label => $url)
        {
            list($label, $url) = $this->getLabelAndUrl($label, $url);

            $html .= $this->menuItem($label, $url);
        }

        return $html;
    }

    /**
     * Generate a list of menu items with maximum items number visible.
     *
     * @param array  $items
     * @param int    $limit
     * @param string $limitLabel
     *
     * @return string
     */
    public function menuItemsWithLimit(array $items, $limit, $limitLabel = 'Show more')
    {
        $html = '';
        $index = 0;
        $hasMore = count($items) > $limit;

        foreach ($items as $label => $url)
        {
            list($label, $url) = $this->getLabelAndUrl($label, $url);

            $html .= $this->menuItem($label, $url, $hasMore && ++$index >= $limit ? [ 'class' => 'hidden' ] : []);
        }

        if ($hasMore) $html .= $this->menuItem($limitLabel, '#', [ 'class' => 'show-more' ]);

        return $html;
    }

    /**
     * Get a label and a url.
     *
     * @param string $label
     * @param string $url
     *
     * @return array
     */
    protected function getLabelAndUrl($label, $url)
    {
        if (is_numeric($label))
        {
            $label = $url;
            $url = '#';
        }

        return [ $label, $url ];
    }

    /**
     * Generate a menu item.
     *
     * @param string      $label
     * @param string|null $url
     * @param array       $attributes
     *
     * @return string
     */
    public function menuItem($label, $url = null, array $attributes = [])
    {
        $label = $this->entities($label);
        
        if ($this->isActiveUrl($url)) $this->appendClass($attributes, 'active');

        if ($url)
        {
            $label = '<a href="' . $url . '">' . $label . '</a>';
        }

        $attributes = $this->attributes($attributes);

        return "<li{$attributes}>{$label}</li>" . PHP_EOL;
    }

    /**
     * Append a class name to the attributes array.
     *
     * @param array        $attributes
     * @param string|array $class
     *
     * @return void
     */
    public function appendClass(array &$attributes, $class)
    {
        $class = implode(' ', (array)$class);

        $attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' ' . $class : $class;
    }

    /**
     * Get whether a url is active.
     *
     * @param string $url
     *
     * @return bool
     */
    public function isActiveUrl($url)
    {
        if ( ! $url || $url === '#') return false;

        // Check if url leads to the main page
        if ($url === $this->request->root())
        {
            return $this->request->path() === '/';
        }

        // Remove query string
        $url = preg_replace('/\?.*/', '', $url) . '*';

        return str_is($url, $this->request->url());
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
            $messages = $messages->toArray();
        }

        $html = '<ul class="errors">';

        foreach ($messages as $message)
        {
            $html .= '<li>'.$message.'</li>';
        }

        return $html.'</ul>';
    }

    public function alerts()
    {
        $html = '';

        foreach ([ 'success', 'danger', 'warning', 'info' ] as $alert)
        {
            if ($this->session->has($alert))
            {
                $html .= $this->alert($this->session->get($alert), $alert, true).PHP_EOL;
            }
        }

        return $html;
    }
}