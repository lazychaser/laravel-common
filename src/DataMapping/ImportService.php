<?php

namespace Kalnoy\LaravelCommon\DataMapping;

use Illuminate\Support\Collection;
use Kalnoy\LaravelCommon\Contracts\DataMapping\DataSource as DataSourceContract;
use Kalnoy\LaravelCommon\Contracts\DataMapping\Importer as ImporterContract;
use Kalnoy\LaravelCommon\Contracts\DataMapping\Repository;
use Kalnoy\LaravelCommon\DataMapping\Events\BatchWasImported;
use Kalnoy\LaravelCommon\DataMapping\Events\BatchWillBeImported;
use Kalnoy\LaravelCommon\DataMapping\Events\ImportHasEnded;
use Kalnoy\LaravelCommon\DataMapping\Events\ImportWillBeStarted;
use Kalnoy\LaravelCommon\DataMapping\Exceptions\ModelValidationException;
use Monolog\Handler\NullHandler;
use Monolog\Logger;

class ImportService
{
    /**
     * @var DataSourceContract
     */
    protected $dataSource;

    /**
     * @var ImporterContract
     */
    protected $importer;

    /**
     * @var int
     */
    protected $mode = Repository::ALL;

    /**
     * @var int
     */
    protected $batchSize = 100;

    /**
     * @var Logger
     */
    protected $log;

    /**
     * @var null|array
     */
    protected $attributes;

    /**
     * @param DataSourceContract $dataSource
     * @param ImporterContract $importer
     */
    public function __construct(DataSourceContract $dataSource,
                                ImporterContract $importer
    ) {
        $this->dataSource = $dataSource;
        $this->importer = $importer;
    }

    /**
     * Do the import.
     *
     * @return ImportStats
     * @throws \Exception
     */
    public function import()
    {
        $this->ensureLogger();

        $stats = new ImportStats;

        event(new ImportWillBeStarted($this));

        $this->log->addInfo('Import started.');

        try {
            $this->dataSource->start();

            while ($items = $this->dataSource->get($this->batchSize)) {
                $stats->merge($this->importItems($items));
            }
        }

        catch (\Exception $e) {
            $this->log->addError($this->formatException($e));
        }

        finally {
            $this->dataSource->stop();
        }

        event(new ImportHasEnded($this, $stats));

        $this->log->info('Import done.', compact('stats'));

        return $stats;
    }

    /**
     * @param Collection $items
     *
     * @return ImportStats
     */
    protected function importItems(Collection $items)
    {
        $this->log->info('Importing a batch of '.$items->count().' item(s)...');

        event(new BatchWillBeImported($this, $items, $this->attributes));

        $items = $this->importer->startBatch($items);

        $result = new ImportStats;

        foreach ($items as $item) {
            try {
                $model = $this->importer->import($item,
                                                 $this->attributes,
                                                 $this->mode);

                $result->imported($model);
            }

            catch (\Exception $e) {
                $this->log->addError($this->formatException($e));

                $result->errored();
            }
        }

        $this->importer->endBatch();

        event(new BatchWasImported($this, $result));

        $this->log->info('Batch done.', compact('result'));

        return $result;
    }

    /**
     * @return void
     */
    protected function ensureLogger()
    {
        if ($this->log) return;

        $this->log = new Logger('importer', [ new NullHandler() ]);
    }

    /**
     * @param \Exception $e
     *
     * @return string
     */
    private function formatException($e)
    {
        if ($e instanceof ModelValidationException) {
            return $this->formatValidationException($e);
        }

        return $e;
    }

    /**
     * @param ModelValidationException $e
     *
     * @return string
     */
    protected function formatValidationException(ModelValidationException $e)
    {
        $text = 'Validation failed for ['.$e->getId().']:'.PHP_EOL;

        $text .= implode(PHP_EOL, $e->errors()->all());

        return $text;
    }

    /**
     * @return ImporterContract
     */
    public function getImporter()
    {
        return $this->importer;
    }

    /**
     * @return DataSourceContract
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setMode($value)
    {
        $this->mode = $value;

        return $this;
    }

    /**
     * @return int
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function setBatchSize($value)
    {
        $this->batchSize = $value;

        return $this;
    }

    /**
     * @return int
     */
    public function getBatchSize()
    {
        return $this->batchSize;
    }

    /**
     * @param Logger $log
     *
     * @return $this
     */
    public function setLogger(Logger $log)
    {
        $this->log = $log;

        return $this;
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->log;
    }

    /**
     * @param array|null $attributes
     *
     * @return $this
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

}