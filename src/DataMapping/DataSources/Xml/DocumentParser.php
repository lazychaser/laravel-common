<?php

namespace Kalnoy\LaravelCommon\DataMapping\DataSources\Xml;

use Kalnoy\LaravelCommon\Contracts\DataMapping\XmlAttributeType;

class DocumentParser
{
    /**
     * @var string
     */
    protected $rootElementName;

    /**
     * @var XmlAttributeType
     */
    protected $rootElementType;

    /**
     * DocumentParser constructor.
     *
     * @param string $name
     * @param XmlAttributeType $type
     */
    public function __construct($name, XmlAttributeType $type)
    {
        $this->rootElementType = $type;
        $this->rootElementName = $name;
    }

    /**
     * @param $uri
     *
     * @return array|bool
     */
    public function parse($uri)
    {
        $reader = new \XMLReader();

        $reader->open($uri);

        try {
            while ($reader->read() && $reader->nodeType != \XMLReader::ELEMENT);

            if ($reader->name != $this->rootElementName) {
                return false;
            }

            return $this->rootElementType->parse($reader);
        }

        finally {
            $reader->close();
        }
    }
}