<?php

namespace Kalnoy\LaravelCommon\DataMapping\Eloquent\Scheme;

use Kalnoy\LaravelCommon\DataMapping\Eloquent\BaseEloquentRepository;
use Kalnoy\LaravelCommon\Contracts\DataMapping\Repository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

abstract class BaseRelation extends AbstractAttribute
{
    /**
     * @var string|BaseEloquentRepository
     */
    protected $repository;

    /**
     * @var int
     */
    protected $mode = Repository::ALL;

    /**
     * @param string $id
     * @param string $repository
     * @param null|string $attribute
     */
    public function __construct($id, $repository, $attribute = null)
    {
        parent::__construct($id, $attribute);

        $this->repository = $repository;
    }

    /**
     * @return BaseEloquentRepository
     */
    public function getRepository()
    {
        if (is_string($this->repository)) {
            return $this->repository = app($this->repository);
        }

        return $this->repository;
    }

    /**
     * @inheritDoc
     */
    public function parse($value)
    {
        return $this->getRepository()->parseKey($value);
    }

    /**
     * @return $this
     */
    public function useOnlyExistingModels()
    {
        $this->mode = Repository::EXISTING;

        return $this;
    }

    /**
     * @param Model $model
     * @param $expecting
     *
     * @return Relation
     */
    protected function getRelation(Model $model, $expecting)
    {
        $relation = $model->{$this->attribute}();

        if ( ! is_a($relation, $expecting)) {
            throw new \RuntimeException("The relation [{$this->attribute}] is not an instance of [{$expecting}].");
        }

        return $relation;
    }

}