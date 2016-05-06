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
     * @var array
     */
    public $models;

    /**
     * Create a new event instance.
     *
     * @param ImportService $importer
     * @param ImportStats $stats
     * @param array $models
     */
    public function __construct(ImportService $importer, ImportStats $stats, 
                                array $models
    ) {
        $this->service = $importer;
        $this->stats = $stats;
        $this->models = $models;
    }

}
