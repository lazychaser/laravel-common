<?php

namespace Kalnoy\LaravelCommon\DataMapping\Events;

use Kalnoy\LaravelCommon\DataMapping\ImportService;

class ImportWillBeStarted
{
    /**
     * @var ImportService
     */
    public $service;

    /**
     * @param ImportService $service
     */
    public function __construct(ImportService $service)
    {
        $this->service = $service;
    }

}