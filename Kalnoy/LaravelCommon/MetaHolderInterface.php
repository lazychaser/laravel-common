<?php

namespace Kalnoy\LaravelCommon;

/**
 * Meta holder interface.
 */
interface MetaHolderInterface {

    /**
     * Get a meta title.
     *
     * @return string
     */
    public function getMetaTitle();

    /**
     * Get meta keywords.
     *
     * @return string
     */
    public function getMetaKeywords();

    /**
     * Get meta description.
     *
     * @return string
     */
    public function getMetaDescription();

}