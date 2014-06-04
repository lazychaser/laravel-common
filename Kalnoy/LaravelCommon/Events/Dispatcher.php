<?php

namespace Kalnoy\LaravelCommon\Events;

use Illuminate\Events\Dispatcher as LaravelDispatcher;

/**
 * Events dispatcher.
 */
class Dispatcher {

    /**
     * @var \Illuminate\Events\Dispatcher
     */
    protected $dispatcher;

    public function __construct(LaravelDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Dispatch events of the events provider.
     *
     * @param \Kalnoy\LaravelCommon\Events\EventsProvider $provider
     *
     * @return void
     */
    public function dispatch(EventsProvider $provider)
    {
        foreach ($provider->events() as $event)
        {
            $this->dispatchEvent($event);
        }
    }

    /**
     * Dispatch single event.
     *
     * @param \Kalnoy\LaravelCommon\EventInterface $event
     *
     * @return void
     */
    public function dispatchEvent(EventInterface $event)
    {
        $this->dispatcher->fire($event->getId(), [ $event ]);
    }

}