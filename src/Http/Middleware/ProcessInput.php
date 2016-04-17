<?php

namespace Kalnoy\LaravelCommon\Http\Middleware;

use Closure;

class ProcessInput
{
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

        $request->merge($this->process($request->except($except)));

        return $next($request);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function process(array $data)
    {
        array_walk_recursive($data, function (&$value, $key) {
            $value = $this->processValue($value, $key);
        });

        return $data;
    }

    /**
     * @param mixed $value
     * @param string $key
     *
     * @return mixed
     */
    protected function processValue($value, $key)
    {
        if (is_string($value)) {
            $value = trim($value);

            if ($value === '') return null;
        }

        return $value;
    }
}