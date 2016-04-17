<?php

namespace Kalnoy\LaravelCommon\DataMapping\DataSources\Xml;

use Kalnoy\LaravelCommon\Contracts\DataMapping\XmlAttributeType;
use Kalnoy\LaravelCommon\DataMapping\DataSources\Xml\Collection;
use XMLReader;

abstract class AbstractComplexXmlAttribute implements XmlAttributeType
{
    /**
     * @var array
     */
    protected $properties = [];

    /**
     * @param $name
     * @param string $type
     *
     * @return $this
     */
    protected function prop($name, $type = 'string')
    {
        $this->properties[$name] = $type;

        return $this;
    }

    /**
     * @param array $properties
     *
     * @return $this
     */
    protected function props(array $properties)
    {
        foreach ($properties as $name => $type) {
            if (is_numeric($name)) {
                $name = $type;
                $type = 'string';
            }

            $this->prop($name, $type);
        }

        return $this;
    }

    /**
     * @param XMLReader $reader
     *
     * @return array
     */
    public function parse(XMLReader $reader)
    {
        if ( ! $reader->read() || $reader->nodeType == XMLReader::END_ELEMENT) {
            return null;
        }

        $data = [];

        do {
            if ( ! $reader->nodeType == XMLReader::ELEMENT) {
                continue;
            }

            if ($this->hasProperty($reader->name)) {
                $data[$reader->name] = $this->parseProperty($reader);
            }
        } while ($reader->next() && $reader->nodeType != XMLReader::END_ELEMENT);

        return $data;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    protected function hasProperty($name)
    {
        return isset($this->properties[$name]);
    }

    /**
     * @param XMLReader $reader
     *
     * @return string
     */
    protected function parseProperty(XMLReader $reader)
    {
        switch ($type = $this->properties[$reader->name]) {
            case 'string': return $reader->readInnerXml();

            default:
                if (is_string($type)) {
                    $type = app($type);
                }

                return $type->parse($reader);
        }
    }

    /**
     * @param string $name
     *
     * @return Collection
     */
    public static function collection($name = null)
    {
        if (is_null($name)) {
            $name = class_basename(static::class);
        }
        
        return new Collection($name, static::class);
    }
}