<?php

namespace Kalnoy\LaravelCommon\Http\Middleware;

use Closure;

class ProcessInput {

    /**
     * @var array
     */
    protected $except = [ 'password', 'password_confirmation' ];

    /**
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     */
    public function handle($request, Closure $next)
    {
        $except = array_merge($this->except, array_slice(func_get_args(), 2));

        $request->merge($this->walk($request->except($except)));

        return $next($request);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function walk(array $data)
    {
        foreach ($data as $key => $value)
        {
            $data[$key] = is_array($value) ? $this->walk($value) : $this->processValue($value);
        }

        return $data;
    }

    /**
     * @param $value
     *
     * @return string
     */
    protected function processValue($value)
    {
        if (is_string($value))
        {
            $value = trim($value);

            if ($value === '') return null;
        }

        return $value;
    }
}