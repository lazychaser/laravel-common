<?php

namespace Kalnoy\LaravelCommon\Service\Form;

use Illuminate\Support\MessageBag;
use Kalnoy\LaravelCommon\Events\EventsProvider;
use Kalnoy\LaravelCommon\Events\EventInterface;
use Kalnoy\LaravelCommon\Service\Validation\ValidableInterface;

abstract class BaseForm implements FormInterface, EventsProvider {
    
    /**
     * The id of the form.
     * 
     * @var string
     */
    protected $id;

    /**
     * The result of processing.
     * 
     * @var \Kalnoy\LaravelCommon\Service\Form\Alert
     */
    protected $message;

    /**
     * The errors.
     * 
     * @var \Illuminate\Support\MessageBag
     */
    protected $errors;

    /**
     * The events.
     * 
     * @var array
     */
    private $events = [];

    /**
     * Init form.
     */
    public function __construct()
    {
        $this->errors = new MessageBag;
    }

    /**
     * Validate an input using validator.
     * 
     * @param array $input
     * @param \Kalnoy\LaravelCommon\Service\Validation\ValidableInterface $validator
     * 
     * @return bool
     */
    protected function valid(array $input, ValidableInterface $validator)
    {
        if ( ! $validator) return true;

        if ( ! $validator->with($input)->passes())
        {
            $this->errors->merge($validator->errors());

            return false;
        }

        return true;
    }

    /**
     * Set an alert message.
     * 
     * @return $this
     */
    protected function alert($type, $message)
    {
        $this->message = new Alert($type, $message, $this->id);

        return $this;
    }

    /**
     * Alert success.
     */
    protected function alertSuccess($message)
    {
        return $this->alert('success', $message);
    }

    /**
     * Alert info.
     */
    protected function alertInfo($message)
    {
        return $this->alert('info', $message);
    }

    /**
     * Alert warning.
     */
    protected function alertWarning($message)
    {
        return $this->alert('warning', $message);
    }

    /**
     * Alert error.
     */
    protected function alertError($message)
    {
        return $this->alert('danger', $message);
    }

    /**
     * Raise an event.
     * 
     * @param \Kalnoy\LaravelCommon\Events\EventInterface $event
     * 
     * @return $this
     */
    public function raise(EventInterface $event)
    {
        $this->events[] = $event;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function field($name)
    {
        if ($this->id) $name = $this->id.'.'.$name;

        return key_to_name($name);
    }

    /**
     * {@inheritdoc}
     */
    public function message()
    {
        return $this->message;
    }

    /**
     * {@inheritdoc}
     */
    public function errors()
    {
        return $this->transformKeys($this->errors->getMessages());
    }

    /**
     * Add a form id to the fields.
     *
     * @param array $data
     *
     * @return array
     */
    protected function transformKeys(array $data)
    {
        if ($this->id === null) return $data;

        $result = [];

        foreach ($data as $key => $value)
        {
            $result[$this->id.'.'.$key] = $value;
        }

        return $result;
    }

    /**
     * Add an error for the attribute.
     *
     * @param string $attribute
     * @param string $message
     */
    protected function addError($attribute, $message)
    {
        $this->errors->add($attribute, trans($message));
    }

    /**
     * {@inheritdoc}
     */
    public function events()
    {
        return $this->events;
    }

    /**
     * {@inheritdoc}
     */
    public function id()
    {
        return $this->id;
    }

}