<?php

namespace Kalnoy\LaravelCommon\Html;

use Illuminate\Http\Request;
// use Illuminate\Html\HtmlBuilder;
use Illuminate\Routing\UrlGenerator;

/**
 * Menu builder.
 */
class MenuBuilder {

    /**
     * HTML builder.
     *
     * @var \Illuminate\Html\HtmlBuilder
     */
    protected $html;

    /**
     * URL generator.
     *
     * @var \Illuminate\Routing\UrlGenerator.
     */
    protected $url;

    /**
     * The request.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * The list of reserved options.
     *
     * @var array
     */
    protected $reserved = [ 'href', 'url', 'route', 'items', 'badge', 'icon' ];

    /**
     * @param HtmlBuilder $html
     * @param UrlGenerator $url
     * @param Request $request
     */
    public function __construct(HtmlBuilder $html, UrlGenerator $url, Request $request)
    {
        $this->html = $html;
        $this->url = $url;
        $this->request = $request;
    }

    /**
     * Render menu.
     *
     * @param array $items
     * @param array $attributes
     *
     * @return string
     */
    public function render($items, array $attributes = [])
    {
        $attributes = $this->html->attributes($attributes);

        return '<ul'.$attributes.'>'.PHP_EOL.$this->items($items).'</ul>';
    }

    /**
     * Render menu items.
     *
     * @param array $items
     *
     * @return string
     */
    public function items($items)
    {
        $html = '';

        foreach ($items as $label => $options)
        {
            list($label, $options) = $this->getLabelAndOptions($label, $options);

            $html .= $this->item($label, $options) . PHP_EOL;
        }

        return $html;
    }

    /**
     * Get the label and options from array $key and $value
     *
     * @param mixed $label
     * @param mixed $options
     *
     * @return array [ $label, $options ]
     */
    public function getLabelAndOptions($label, $options)
    {
        if ( ! is_array($options))
        {
            if (is_numeric($label))
            {
                $label = $options;
                $options = [];
            }
            else
            {
                $options = [ 'href' => $options ];
            }
        }

        return [ $label, $options ];
    }

    /**
     * Render a menu item.
     *
     * @param string $label
     * @param array  $options
     *
     * @return string
     */
    public function item($label, array $options = [])
    {
        $href = $this->getHref($options);
        $link = $this->getLink($href, $label, $options);

        $attributes = array_except($options, $this->reserved);

        if ($this->isActive($href))
        {
            $this->html->appendClass($attributes, 'active');
        }

        $attributes = $this->html->attributes($attributes);

        $html = '<li'.$attributes.'>'.$link;

        if (isset($options['items']))
        {
            $html .= $this->render($options['items'], [ 'class' => 'dropdown-menu' ]);
        }

        return $html . '</li>';
    }

    /**
     * Get menu item link.
     *
     * @param string $href
     * @param string $label
     *
     * @return string
     */
    protected function getLink($href, $label, array $options)
    {
        $attributes = [];

        $label = $this->html->entities(trans($label));

        if (isset($options['badge']))
        {
            $label .= ' '.$this->getBadge($options['badge']);
        }

        if (isset($options['items']))
        {
            $attributes['class'] = 'dropdown-toggle';
            $attributes['data-toggle'] = 'dropdown';

            $label .= $this->getCaret();
        }

        if (isset($options['icon']))
        {
            $label = $this->getIcon($options['icon']).' '.$label;
        }

        $attributes['href'] = $href;

        return '<a'.$this->html->attributes($attributes).'>'.$label.'</a>';
    }

    /**
     * Get href from options.
     *
     * @param array $options
     *
     * @return string
     */
    protected function getHref($options)
    {
        if (isset($options['href'])) return $options['href'];

        if (isset($options['url'])) return $this->getUrlHref($options['url']);

        if (isset($options['route'])) return $this->getRouteHref($options['route']);

        return $this->url->current();
    }

    /**
     * Get href from url.
     *
     * @param array|string $url
     *
     * @return string
     */
    public function getUrlHref($url)
    {
        if (is_array($url))
        {
            return $this->url->to($url[0], array_slice($url, 1));
        }

        return $this->url->to($url);
    }

    /**
     * Get href from route.
     *
     * @param array|string $route
     *
     * @return string
     */
    protected function getRouteHref($route)
    {
        if (is_array($route))
        {
            return $this->url->route($route[0], array_slice($route, 1));
        }

        return $this->url->route($route);
    }

    /**
     * Get whether a href is active.
     *
     * @param string $href
     *
     * @return bool
     */
    public function isActive($href)
    {
        if ( ! $href || $href === '#') return false;

        // Check if url leads to the main page
        if ($href === $this->request->root())
        {
            return $this->request->path() === '/';
        }

        // Check query string parameters
        if (false !== $pos = strpos($href, '?'))
        {
            $params = [];

            parse_str(substr($href, $pos + 1), $params);

            if ( ! $this->requestHasParameters($params)) return false;

            $href = substr($href, 0, $pos);
        }

        $url = $this->request->url();

        return $url === $href or str_is($href . '/*', $this->request->url());
    }

    /**
     * Check if the request has all needed parameters.
     *
     * @param array $params
     *
     * @return bool
     */
    protected function requestHasParameters($params)
    {
        if (empty($params)) return true;

        $input = $this->request->all();

        foreach ($params as $key => $value)
        {
            if ( ! array_key_exists($key, $input) || $input[$key] != $value) return false;
        }

        return true;
    }

    /**
     * @param $badge
     *
     * @return string
     */
    protected function getBadge($badge)
    {
        return '<span class="badge">'.$badge.'</span>';
    }

    /**
     * @return string
     */
    protected function getCaret()
    {
        return '<span class="caret"></span>';
    }

    /**
     * @param string $icon
     *
     * @return string
     */
    protected function getIcon($icon)
    {
        return '<span class="glyphicon glyphicon-'.$icon.'"></span>';
    }
}