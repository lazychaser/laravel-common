<?php

namespace Kalnoy\LaravelCommon\DataMapping\Exceptions;

use Illuminate\Contracts\Support\MessageProvider;
use Illuminate\Contracts\Validation\ValidationException;

class ModelValidationException extends ValidationException
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @param string $id
     * @param MessageProvider $provider
     */
    public function __construct($id, MessageProvider $provider)
    {
        parent::__construct($provider);

        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

}