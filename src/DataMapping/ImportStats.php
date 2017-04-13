<?php

namespace Kalnoy\LaravelCommon\DataMapping;

use Illuminate\Contracts\Support\MessageBag;
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
     * @var Collection
     */
    public $validationErrors;

    /**
     * ImportStats constructor.
     */
    public function __construct()
    {
        $this->importedItems = new Collection();
        $this->validationErrors = new Collection();
    }

    /**
     * Increment errored items.
     */
    public function errored()
    {
        $this->errored++;
    }

    /**
     * @param $row
     * @param MessageBag $errors
     */
    public function validationFailed($row, MessageBag $errors)
    {
        $this->validationErrors->put($row, $errors);
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
        return $this->importedItems->count()
            + $this->skipped
            + $this->errored
            + $this->validationErrors->count();
    }

    /**
     * @param ImportStats $stats
     */
    public function merge(ImportStats $stats)
    {
        $this->errored += $stats->errored;
        $this->skipped += $stats->skipped;

        $this->importedItems = $this->importedItems->merge($stats->importedItems);
        $this->validationErrors = $this->validationErrors->merge($stats->validationErrors);
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
            'Errored: '.$this->errored.PHP_EOL.
            'Invalid: '.$this->validationErrors->count();
    }
}