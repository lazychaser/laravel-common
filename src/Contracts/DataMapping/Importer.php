<?php

namespace Kalnoy\LaravelCommon\Contracts\DataMapping;

use Illuminate\Support\Collection;

interface Importer
{
    /**
     * Import single data item.
     *
     * @param array $data
     * @param array|null $attributes
     * @param int $mode
     *
     * @return mixed
     */
    public function import(array $data, 
                           $attributes = null, 
                           $mode = Repository::ALL);

    /**
     * Indicate that batch import has started.
     *
     * @param Collection $items
     *
     * @return Collection
     */
    public function startBatch(Collection $items);

    /**
     * Indicate that batch import has ended.
     */
    public function endBatch();

}