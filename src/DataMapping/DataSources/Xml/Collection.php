<?php

namespace Kalnoy\LaravelCommon\DataMapping\DataSources\Xml;

use Kalnoy\LaravelCommon\Contracts\DataMapping\XmlAttributeType;
use XMLReader;

class Collection implements XmlAttributeType
{
    /**
     * @var string
     */
    protected $keyBy;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string|AbstractComplexXmlAttribute
     */
    protected $type;

    /**
     * @var null
     */
    protected $pluck;

    /**
     * Collection constructor.
     *
     * @param string $name
     * @param string|AbstractComplexXmlAttribute $type
     * @param string $keyBy
     * @param null $pluck
     */
    public function __construct($name, $type, $keyBy = null, $pluck = null)
    {
        $this->keyBy = $keyBy;
        $this->name = $name;
        $this->type = $type;
        $this->pluck = $pluck;
    }

    /**
     * @param string $name
     * @param string|AbstractComplexXmlAttribute $type
     *
     * @return static
     */
    public static function make($name, $type)
    {
        return new static($name, $type);
    }

    /**
     * @param XMLReader $reader
     *
     * @return array
     */
    public function parse(XMLReader $reader)
    {
        if ( ! $reader->read()) return null;

        $data = [];

        do {
            if ($reader->nodeType != XMLReader::ELEMENT ||
                $reader->name != $this->name
            ) {
                continue;
            }

            $parsed = $this->getType()->parse($reader);

            $value = $this->pluck ? data_get($parsed, $this->pluck) : $parsed;

            if ($this->keyBy) {
                $key = data_get($parsed, $this->keyBy);

                $data[$key] = $value;
            } else {
                $data[] = $value;
            }
        } while ($reader->next() && $reader->nodeType != XMLReader::END_ELEMENT);

        return $data;
    }

    /**
     * @return AbstractComplexXmlAttribute
     */
    public function getType()
    {
        if (is_string($this->type)) {
            $this->type = app($this->type);
        }

        return $this->type;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function keyBy($value)
    {
        $this->keyBy = $value;
        
        return $this;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function pluck($value)
    {
        $this->pluck = $value;
        
        return $this;
    }

}