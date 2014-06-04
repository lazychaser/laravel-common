<?php

namespace Kalnoy\LaravelCommon\Events;

/**
 * Events provider interface.
 */
interface EventsProvider {

    /**
     * Get a list of events.
     *
     * @return \Kalnoy\LaravelCommon\Events\EventInterface[]
     */
    public function events();

}