<?php

namespace Kalnoy\LaravelCommon\Service\Form;

use Session;
use Illuminate\Support\Contracts\ArrayableInterface;

class Alert implements ArrayableInterface {

    /**
     * The type of the alert (succes, warning, info).
     *
     * @var string
     */
    protected $type;

    /**
     * The alert domain (for multiple alerts).
     *
     * @var string
     */
    protected $domain;

    /**
     * The alert message.
     *
     * @var string
     */
    protected $message;

    /**
     * Init alert.
     *
     * @param string $type
     * @param string $message
     * @param string $domain
     */
    public function __construct($type, $message, $domain = null)
    {   
        $this->type = $type;
        $this->message = trans($message);
        $this->domain = $domain;
    }

    /**
     * Flash the alert to the session.
     *
     * @return void
     */
    public function flash()
    {
        Session::flash($this->getQualifiedType(), $this->message);
    }

    /**
     * Get the type of the alert.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get the qualified (with domain) type of the alert.
     *
     * @return string
     */
    public function getQualifiedType()
    {
        return $this->domain ? $this->domain.'.'.$this->type : $this->type;
    }

    /**
     * Get the message of the alert.
     *
     * @return void
     */ 
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return
        [
            'type' => $this->type,
            'message' => $this->message,
            'domain' => $this->domain,
        ];
    }

}