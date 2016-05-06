<?php

namespace Kalnoy\LaravelCommon\Repo\Pagination;

use Illuminate\Support\Collection;

class FeedPaginator
{
    /**
     * @var Collection
     */
    protected $items;

    /**
     * @var int
     */
    protected $perPage;

    /**
     * @var int
     */
    protected $timestamp;

    /**
     * @var bool
     */
    protected $hasMore;

    /**
     * @var string
     */
    protected $column;

    /**
     * @param $items
     * @param $perPage
     * @param $timestamp
     * @param $column
     */
    public function __construct($items, $perPage, $timestamp, $column)
    {
        $this->items = $items instanceof Collection ? $items : new Collection($items);
        $this->perPage = $perPage;
        $this->timestamp = $timestamp;
        $this->column = $column;

        $this->hasMore = $this->items->count() > $perPage;
        $this->items = $this->items->slice(0, $this->perPage);
    }

    /**
     * @return Collection
     */
    public function getCollection()
    {
        return $this->items;
    }

    /**
     * @return bool
     */
    public function hasMore()
    {
        return $this->hasMore;
    }

    /**
     * @return int
     */
    public function timestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return int
     */
    public function perPage()
    {
        return $this->perPage;
    }

    /**
     * @return int
     */
    public function nextTimestamp()
    {
        return $this->hasMore ? $this->items->last()->{$this->column}->timestamp : null;
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->items->count();
    }

}