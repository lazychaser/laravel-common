<?php

namespace Kalnoy\LaravelCommon\DataMapping\Events;

use Kalnoy\LaravelCommon\DataMapping\ImportService;

use Illuminate\Support\Collection;

class BatchWillBeImported
{
    /**
     * @var ImportService
     */
    public $service;

    /**
     * @var Collection
     */
    public $items;

    /**
     * @var array
     */
    public $attributes;

    /**
     * Create a new event instance.
     *
     * @param ImportService $service
     * @param Collection $items
     * @param array|null $attributes
     */
    public function __construct(ImportService $service, Collection $items,
                                $attributes
    ) {
        $this->service = $service;
        $this->items = $items;
        $this->attributes = $attributes;
    }

}
