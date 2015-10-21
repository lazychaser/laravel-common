<?php

namespace Kalnoy\LaravelCommon\Http;

use Illuminate\Contracts\Routing\UrlGenerator as NativeUrlGenerator;

class UrlGenerator
{
    /**
     * @var array
     */
    protected $entities = [];

    /**
     * @var
     */
    protected $url;

    /**
     * UrlGenerator constructor.
     *
     * @param NativeUrlGenerator $url
     */
    public function __construct(NativeUrlGenerator $url)
    {
        $this->url = $url;
    }

    /**
     * @param $entity
     *
     * @return string
     */
    public function entity($entity, array $params = [])
    {
        $class = get_class($entity);

        if ( ! isset($this->entities[$class])) {
            throw new \RuntimeException;
        }

        $method = $this->entities[$class];

        return $this->$method($entity, $params);
    }
}