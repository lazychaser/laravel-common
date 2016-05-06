<?php

namespace Kalnoy\LaravelCommon\Contracts\DataMapping;

use Illuminate\Support\Collection;

interface DataSource {

    /**
     * @return void
     */
    public function start();

    /**
     * @param int $limit
     *
     * @return Collection
     */
    public function get($limit = 100);

    /**
     * @return void
     */
    public function stop();

    /**
     * Get the list of attributes that this data source has.
     *
     * @return array
     */
    public function getAvailableAttributes();

}