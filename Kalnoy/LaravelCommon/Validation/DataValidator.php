<?php

namespace Kalnoy\LaravelCommon\Validation;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Http\Request;

abstract class DataValidator implements ValidatorContract
{
    /**
     * @var \Illuminate\Validation\Validator
     */
    protected $validator;

    /**
     * @param $data
     */
    public function __construct($data)
    {
        $this->validator = $this->validator($this->arrayableData($data));
    }

    /**
     * @param array $data
     *
     * @return \Illuminate\Validation\Validator
     */
    private function validator(array $data)
    {
        $factory = app(\Illuminate\Validation\Factory::class);

        $validator = $factory->make(
            $data,
            app()->call([ $this, 'rules' ]),
            $this->messages(),
            $this->attributes()
        );

        if (method_exists($this, 'customize')) {
            app()->call([ $this, 'customize' ], [ $validator ]);
        }

        return $validator;
    }

    /**
     * @return array
     */
    protected function messages()
    {
        return [ ];
    }

    /**
     * @return array
     */
    protected function attributes()
    {
        return [ ];
    }

    /**
     * @param $data
     *
     * @return array
     */
    private function arrayableData($data)
    {
        if (is_array($data)) return $data;

        if ($data instanceof Arrayable) return $data->toArray();

        if ($data instanceof Request) return $data->all();

        throw new \RuntimeException;
    }

    /**
     * @inheritDoc
     */
    public function fails()
    {
        return $this->validator->fails();
    }

    /**
     * @inheritDoc
     */
    public function failed()
    {
        return $this->validator->failed();
    }

    /**
     * @return bool
     */
    public function passes()
    {
        return $this->validator->passes();
    }

    /**
     * @inheritDoc
     */
    public function sometimes($attribute, $rules, callable $callback)
    {
        $this->validator->sometimes($attribute, $rules, $callback);
    }

    /**
     * @inheritDoc
     */
    public function after($callback)
    {
        $this->validator->after($callback);
    }

    /**
     * @inheritDoc
     */
    public function getMessageBag()
    {
        return $this->validator->getMessageBag();
    }

    /**
     * @return \Illuminate\Support\MessageBag
     */
    public function errors()
    {
        return $this->validator->errors();
    }

}