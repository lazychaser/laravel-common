<?php

namespace Kalnoy\LaravelCommon\DataMapping\Events;


use Kalnoy\LaravelCommon\DataMapping\ImportService;
use Kalnoy\LaravelCommon\DataMapping\ImportStats;

class BatchWasImported
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
     * Create a new event instance.
     *
     * @param ImportService $importer
     * @param ImportStats $stats
     */
    public function __construct(ImportService $importer, ImportStats $stats)
    {
        $this->service = $importer;
        $this->stats = $stats;
    }

}
