<?php

namespace Kalnoy\LaravelCommon\Events;

/**
 * Event interface.
 */
interface EventInterface {

    /**
     * Get event id.
     *
     * @return string
     */
    public function getId();

}