<?php

namespace Kalnoy\LaravelCommon\DataMapping\Eloquent\Scheme;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo as BelongsToRelation;
use Illuminate\Support\Collection;

class BelongsTo extends BaseRelation
{
    /**
     * Do some stuff before items are imported.
     *
     * @param Collection $items
     *
     * @return $this
     */
    public function preload(Collection $items)
    {
        $this->getRepository()->preload($items->pluck($this->id)->all());

        return $this;
    }

    /**
     * @param array $data
     * @param Model $model
     *
     * @return $this
     */
    public function setOn(array $data, Model $model)
    {
        /** @var BelongsToRelation $relation */
        $relation = $this->getRelation($model, BelongsToRelation::class);

        if (($key = $data[$this->id]) &&
            $model = $this->getRepository()->retrieve($key, $this->mode)
        ) {
            $relation->associate($model);

            return $this;
        }

        $relation->dissociate();

        return $this;
    }

}