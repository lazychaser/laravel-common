<?php

namespace Kalnoy\LaravelCommon\Service\Form;

use Session;
use Illuminate\Support\Contracts\ArrayableInterface;

class Alert implements ArrayableInterface {

    protected $type;

    protected $domain;

    protected $message;

    public function __construct($type, $message, $domain = null)
    {   
        $this->type = $type;
        $this->message = trans($message);
        $this->domain = $domain;
    }

    public function flash()
    {
        Session::flash($this->getQualifiedType(), $this->message);
    }

    public function getType()
    {
        return $this->type;
    }

    public function getQualifiedType()
    {
        return $this->domain ? $this->domain.'.'.$this->type : $this->type;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function toArray()
    {
        return
        [
            'type' => $this->type,
            'message' => $this->message,
        ];
    }

}