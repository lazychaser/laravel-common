<?php

namespace Kalnoy\LaravelCommon\DataMapping\Eloquent;

class EloquentRepository extends BaseEloquentRepository
{
    /**
     * @var string
     */
    protected $className;

    /**
     * @var string
     */
    protected $primaryKey;

    /**
     * @param string $className
     * @param string $primaryKey
     * @param array $columns
     */
    public function __construct($className, $primaryKey = 'text_id',
                                array $columns = [ '*' ]
    ) {
        parent::__construct($columns);

        $this->className = $className;
        $this->primaryKey = $primaryKey;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function newModel()
    {
        return new $this->className;
    }

    /**
     * @return string
     */
    public function primaryKey()
    {
        return $this->primaryKey;
    }
}