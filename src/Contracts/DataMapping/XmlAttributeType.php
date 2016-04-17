<?php

namespace Kalnoy\LaravelCommon\Contracts\DataMapping;

use XMLReader;

interface XmlAttributeType
{
    /**
     * @param XMLReader $reader
     *
     * @return array
     */
    public function parse(XMLReader $reader);
}