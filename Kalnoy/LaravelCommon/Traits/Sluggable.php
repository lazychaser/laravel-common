<?php

namespace Kalnoy\LaravelCommon\Traits;

use Str;

trait Sluggable {

    /**
     * Boot method.
     *
     * @return void
     */
    public static function bootSluggableTrait()
    {
        static::saving(function ($model)
        {
            if ( ! $model->hasSlug())
            {
                $slug = Str::slug(cyrillic_to_latin($model->getName()));

                $model->setSlug($slug);
            } 
        });
    }

    /**
     * Get whether model has slug.
     *
     * @return bool
     */
    public function hasSlug()
    {
        return isset($this->slug);
    }

    /**
     * Set the slug.
     *
     * @param string $value
     */
    public function setSlug($value)
    {
        $this->setAttribute('slug', $value);
    }

    /**
     * Get a model name from which slug will be generated.
     *
     * @return string
     */
    abstract public function getName();
}