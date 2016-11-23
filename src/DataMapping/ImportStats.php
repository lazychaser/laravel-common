<?php

namespace Kalnoy\LaravelCommon\DataMapping;

use Illuminate\Support\Collection;

class ImportStats
{
    /**
     * @var int
     */
    public $errored = 0;

    /**
     * @var int
     */
    public $skipped = 0;

    /**
     * @var Collection
     */
    public $importedItems;

    /**
     * ImportStats constructor.
     */
    public function __construct()
    {
        $this->importedItems = new Collection();
    }

    /**
     * Increment errored items.
     */
    public function errored()
    {
        $this->errored++;
    }

    /**
     * Increment imported items.
     */
    public function imported($item)
    {
        if ($item) {
            $this->importedItems->push($item);
        } else {
            $this->skipped++;
        }
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->importedItems->count() + $this->skipped + $this->errored;
    }

    /**
     * @param ImportStats $stats
     */
    public function merge(ImportStats $stats)
    {
        $this->errored += $stats->errored;
        $this->skipped += $stats->skipped;

        $this->importedItems = $this->importedItems->merge($stats->importedItems);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return
            'Processed: '.$this->getTotal().PHP_EOL.
            'Imported: '.$this->importedItems->count().PHP_EOL.
            'Skipped: '.$this->skipped.PHP_EOL.
            'Errored: '.$this->errored;
    }
}