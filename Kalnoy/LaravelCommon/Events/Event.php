<?php

namespace Kalnoy\LaravelCommon\Events;

class Event implements EventInterface {

    /**
     * Get an id of the event.
     *
     * @return string
     */
    public function getId()
    {
        return str_replace('\\', '.', get_class($this));
    }

}