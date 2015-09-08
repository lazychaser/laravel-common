<?php

namespace Kalnoy\LaravelCommon\Http\Middleware;

use Closure;

class ProtectFormInput {

    /**
     * @var array
     */
    protected $except = [
        'Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {
        if ($request->isMethodSafe() || $request->ajax() || app()->isLocal())
        {
            return $next($request);
        }

        try
        {
            return $next($request);
        }

        catch (\Exception $e)
        {
            if (in_array(get_class($e), $this->except))
            {
                throw $e;
            }

            app('Illuminate\Contracts\Debug\ExceptionHandler')->report($e);

            return back()
                ->withInput()
                ->with('danger', $this->getMessage($e));
        }
    }

    /**
     * @param \Exception $e
     *
     * @return string
     */
    protected function getMessage(\Exception $e)
    {
        return 'К сожалению, при обработке запроса произошла ошибка. Пожалуйста, повторите запрос позже.';
    }
}
