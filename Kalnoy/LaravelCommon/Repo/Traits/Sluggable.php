<?php

namespace Kalnoy\LaravelCommon\Repo\Traits;

trait Sluggable {

    protected $slugAttribute = 'slug';

    /**
     * Find an item by a slug.
     *
     * @param string $slug
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function bySlug($slug)
    {
        return $this->findModel(function ($q) use ($slug)
        {
            $q->where($this->slugAttribute, '=', $slug);
        });
    }
}