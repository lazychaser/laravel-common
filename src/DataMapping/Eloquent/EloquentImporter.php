<?php

namespace Kalnoy\LaravelCommon\DataMapping\Eloquent;

use Kalnoy\LaravelCommon\Contracts\DataMapping\Attribute;
use Kalnoy\LaravelCommon\Contracts\DataMapping\Attribute as AttributeContract;
use Kalnoy\LaravelCommon\Contracts\DataMapping\Importer;
use Kalnoy\LaravelCommon\Contracts\DataMapping\Repository;
use Kalnoy\LaravelCommon\DataMapping\Eloquent\Events\ModelWasImported;
use Kalnoy\LaravelCommon\DataMapping\Exceptions\ModelValidationException;
use Kalnoy\LaravelCommon\DataMapping\Eloquent\Scheme\AbstractAttribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class EloquentImporter implements Importer
{
    /**
     * @var Collection|Attribute[]
     */
    protected $scheme;

    /**
     * @var BaseEloquentRepository
     */
    protected $repository;

    /**
     * @param BaseEloquentRepository $repository
     */
    public function __construct(BaseEloquentRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Primary key name on external data.
     *
     * By default, model primary key from repository is used.
     *
     * @return string
     */
    protected function primaryKey()
    {
        return $this->repository->primaryKey();
    }

    /**
     * Defines a list of attributes.
     *
     * @return AbstractAttribute[]
     */
    protected function scheme()
    {
        return [ ];
    }

    /**
     * @inheritdoc
     */
    public function import(array $data, $attributes = null,
                           $mode = Repository::ALL
    ) {
        if ( ! ($key = array_get($data, $this->primaryKey())) ||
            ! ($model = $this->repository->retrieve($key, $mode))
        ) {
            return false;
        }

        $this->validate($key, $data);

        if ($this->save($model, $data, $attributes)) {
            event(new ModelWasImported($model));
        }

        return $model;
    }

    /**
     * @param Model $model
     * @param array $data
     * @param array $attributes
     *
     * @return Model
     */
    protected function fill(Model $model, array $data, $attributes = null)
    {
        $scheme = $this->getScheme();

        if (is_null($attributes)) {
            foreach ($scheme as $id => $attr) {
                if (array_key_exists($id, $data)) {
                    $attr->setOn($data, $model);
                }
            }

            return $model;
        }

        foreach ($attributes as $attr) {
            if ($scheme->has($attr) && array_key_exists($attr, $data)) {
                $scheme->get($attr)->setOn($data, $model);
            }
        }

        return $model;
    }

    /**
     * @param Model $model
     * @param array $data
     * @param $attributes
     *
     * @return bool
     */
    protected function save($model, array $data, $attributes)
    {
        $this->fill($model, $data, $attributes);

        return $model->save();
    }

    /**
     * @param Collection $items
     *
     * @return $this
     */
    protected function preloadModels(Collection $items)
    {
        $this->repository->preload($items->pluck($this->primaryKey())->all());

        return $this;
    }

    /**
     * @param Collection $items
     *
     * @return $this
     */
    protected function preloadAttributes(Collection $items)
    {
        foreach ($this->getScheme() as $attribute) {
            $attribute->preload($items);
        }

        return $this;
    }

    /**
     * @param $key
     * @param array $data
     */
    protected function validate($key, array $data)
    {
        $validator = \Validator::make($data, $this->rules($key));

        if ($validator->fails()) {
            throw new ModelValidationException($key, $validator);
        }
    }

    /**
     * @param string $key
     *
     * @return array
     */
    protected function rules($key)
    {
        return [ ];
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function parse(array $data)
    {
        $scheme = $this->getScheme();

        foreach ($data as $key => $value) {
            if ($scheme->has($key)) {
                $data[$key] = $scheme->get($key)->parse($value);
            }
        }

        $pk = $this->primaryKey();

        if (array_key_exists($pk, $data)) {
            $data[$pk] = $this->repository->parseKey($data[$pk]);
        }

        return $data;
    }

    /**
     * Indicate that batch import has started.
     *
     * @param Collection $items
     *
     * @return Collection
     */
    public function startBatch(Collection $items)
    {
        $items = $items->map([ $this, 'parse' ]);

        $this->beginTransaction()
             ->preloadModels($items)
             ->preloadAttributes($items);

        return $items;
    }

    /**
     * Indicate that batch import has ended.
     *
     * @internal param Collection $items
     */
    public function endBatch()
    {
        $this->commitTransaction();
    }

    /**
     * Begin transaction.
     *
     * @return $this
     */
    protected function beginTransaction()
    {
        $this->repository->newModel()->getConnection()->beginTransaction();

        return $this;
    }

    /**
     * Commit transaction.
     *
     * @return $this
     */
    protected function commitTransaction()
    {
        $this->repository->newModel()->getConnection()->commit();

        return $this;
    }

    /**
     * @return AttributeContract[]|Collection
     */
    public function getScheme()
    {
        if (is_null($this->scheme)) {
            $this->scheme = new Collection;

            foreach ($this->scheme() as $attr) {
                $this->scheme->put($attr->getId(), $attr);
            }
        }

        return $this->scheme;
    }

    /**
     * @return BaseEloquentRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }
}