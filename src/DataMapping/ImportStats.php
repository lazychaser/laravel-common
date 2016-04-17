<?php

namespace Kalnoy\LaravelCommon\DataMapping;

class ImportStats
{
    /**
     * @var int
     */
    public $errored = 0;

    /**
     * @var int
     */
    public $imported = 0;

    /**
     * @var int
     */
    public $skipped = 0;

    /**
     * Increment errored items.
     */
    public function errored()
    {
        $this->errored++;
    }

    /**
     * Increment skipped items.
     */
    public function skipped()
    {
        $this->skipped++;
    }

    /**
     * Increment imported items.
     */
    public function imported()
    {
        $this->imported++;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->imported + $this->skipped + $this->errored;
    }

    /**
     * @param ImportStats $stats
     */
    public function merge(ImportStats $stats)
    {
        $this->errored += $stats->errored;
        $this->imported += $stats->imported;
        $this->skipped += $stats->skipped;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return
            'Processed: '.$this->getTotal().PHP_EOL.
            'Imported: '.$this->imported.PHP_EOL.
            'Skipped: '.$this->skipped.PHP_EOL.
            'Errored: '.$this->errored;
    }
}