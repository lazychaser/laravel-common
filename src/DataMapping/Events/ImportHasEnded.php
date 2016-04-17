<?php

namespace Kalnoy\LaravelCommon\DataMapping\Events;

use Kalnoy\LaravelCommon\DataMapping\ImportService;
use Kalnoy\LaravelCommon\DataMapping\ImportStats;

class ImportHasEnded
{
    /**
     * @var ImportService
     */
    public $service;

    /**
     * @var ImportStats
     */
    public $stats;

    /**
     * @param ImportService $service
     * @param ImportStats $stats
     */
    public function __construct(ImportService $service, ImportStats $stats)
    {
        $this->service = $service;
        $this->stats = $stats;
    }
}