<?php

namespace Kalnoy\LaravelCommon\Repo;

/**
 * Repository that has method to query a model by a slug.
 */
interface SluggableRepositoryInterface {

    /**
     * Get a model by a slug.
     *
     * @param string $slug
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function bySlug($slug);  
}