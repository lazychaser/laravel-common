<?php

namespace Kalnoy\LaravelCommon\Service\Form;

use Kalnoy\LaravelCommon\Service\Validation\ValidableInterface;
use Kalnoy\LaravelCommon\Events\EventsProvider;

abstract class AbstractForm implements EventsProvider {

    /**
     * The validator.
     *
     * @var \App\Service\Validation\ValidableInterface
     */
    protected $validator;

    /**
     * The list of raised events.
     */
    protected $events = [];

    public function __construct(ValidableInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * Validate and input using validator.
     *
     * @param  array  $input
     *
     * @return bool
     */
    public function valid(array $input)
    {
        return $this->validator->with($input)->passes();
    }

    /**
     * Get validation errors.
     *
     * @return array
     */
    function errors()
    {
        return $this->validator->errors();
    }

    /**
     * Raise an event.
     *
     * @param StdClass $event
     *
     * @return void
     */
    protected function raise($event)
    {
        $this->events[] = $event;
    }

    /**
     * Get and clear events.
     *
     * @return array
     */
    public function events()
    {
        $events = $this->events;

        $this->events = [];

        return $events;
    }
}