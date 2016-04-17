<?php

namespace Kalnoy\LaravelCommon\DataMapping\DataSources;

use Kalnoy\LaravelCommon\Contracts\DataMapping\DataSource;
use Kalnoy\LaravelCommon\DataMapping\Exceptions\ConverterException;
use Illuminate\Support\Collection;

class CSV implements DataSource
{
    /**
     * @var string
     */
    protected $sourceEncoding;

    /**
     * @var string
     */
    protected $targetEncoding = 'UTF-8';

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var array
     */
    protected $header;

    /**
     * @var CSV delimiter
     */
    protected $delimiter = ',';

    /**
     * @var resource
     */
    protected $f;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * @param string $filename
     * @param string $sourceEncoding
     */
    public function __construct($filename, $sourceEncoding = 'Windows-1251')
    {
        $this->sourceEncoding = $sourceEncoding;
        $this->filename = $filename;
    }

    /**
     * Start the reading.
     */
    public function start()
    {
        if ( ! $this->f = fopen($this->filename, 'r')) {
            throw new \RuntimeException('Couldn not read the file.');
        }

        $this->header = $this->readHeader();
    }

    /**
     * @param int $limit
     *
     * @return Collection
     */
    public function get($limit = 100)
    {
        if ( ! $this->f) {
            $this->start();
        }

        $data = new Collection;

        while ($limit-- > 0 && $row = $this->read()) {
            $data->push($this->convertRow($row));
        }

        return $data->isEmpty() ? false : $data;
    }

    /**
     * Finalize the data source.
     */
    public function stop()
    {
        if ($this->f) {
            fclose($this->f);

            $this->f = null;
        }
    }

    /**
     * Read header and return available fields.
     *
     * @return array|false
     */
    protected function readHeader()
    {
        if (false === $row = $this->read()) {
            throw new ConverterException('Could not read CSV header.');
        }

        $header = [ ];

        foreach ($row as $index => $value) {
            if ($col = $this->parseHeaderCell($value, $index)) {
                $header[] = $col;
            }
        }

        if (empty($header)) {
            throw new ConverterException('CSV header is empty.');
        }

        return $header;
    }

    /**
     * Extract value.
     *
     * @param string $value
     *
     * @return string|null
     */
    protected function value($value)
    {
        $value = trim($value);

        if ($value === '' || $value === '-') return null;

        if ($this->sourceEncoding == $this->targetEncoding) return $value;

        return iconv($this->sourceEncoding, $this->targetEncoding, $value);
    }

    /**
     * Convert single row.
     *
     * @param array $data
     *
     * @return array
     */
    protected function convertRow(array $data)
    {
        $attributes = $this->getAvailableAttributes();

        $row = array_combine($attributes,
                             array_pad([], count($attributes), null));

        foreach ($this->header as $col) {
            if (null !== $value = $this->value($data[$col['index']])) {
                array_set($row, $col['key'], $value);
            }
        }

        return $row;
    }

    /**
     * Read CSV line.
     *
     * @return array
     */
    protected function read()
    {
        return fgetcsv($this->f, 0, $this->delimiter);
    }

    /**
     * @param string $value
     * @param int $index
     *
     * @return bool|string
     */
    protected function parseHeaderCell($value, $index)
    {
        if ( ! $value = $this->value($value)) return false;

        if (preg_match('/(.*)\(([a-z0-9_.]+)\)\s*$/i', $value, $matches)) {
            return [ 'title' => trim($matches[1]), 'key' => $matches[2], 'index' => $index ];
        }

        return false;
    }

    /**
     * @param string $value
     */
    public function setDelimiter($value)
    {
        $this->delimiter = $value;
    }

    /**
     * @param string $value
     */
    public function setTargetEncoding($value)
    {
        $this->targetEncoding = $value;
    }

    /**
     * Available after `convert` is executed.
     *
     * @return array|null
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @return array
     */
    public function getAvailableAttributes()
    {
        if ( ! is_null($this->attributes)) return $this->attributes;

        $keys = array_pluck($this->header, 'key');

        $keys = array_map(function ($key) {
            if (false !== $pos = strpos($key, '.')) {
                return substr($key, 0, $pos);
            }

            return $key;
        }, $keys);

        return $this->attributes = array_unique($keys);
    }

}