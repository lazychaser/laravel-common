<?php

namespace Kalnoy\LaravelCommon\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Foundation\Validation\ValidationException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    protected $validationFailedMessage;

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $e
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof ValidationException) {
            $this->addFailedValidationAlert($e);
        }

        return parent::render($request, $e);
    }

    /**
     * @param HttpException $e
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderHttpException(HttpException $e)
    {
        if ($this->isNonTextRequest(app('request'))) {
            return response('', $e->getStatusCode());
        }

        return parent::renderHttpException($e);
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function isNonTextRequest($request)
    {
        if ($request->ajax()) return true;

        return ! in_array('text/html', $request->getAcceptableContentTypes());
    }

    /**
     * @param ValidationException $e
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function addFailedValidationAlert(ValidationException $e)
    {
        if (! $this->validationFailedMessage) return;

        $response = $e->getResponse();

        if ( ! $response instanceof JsonResponse) {
            $response->with('warning', trans($this->validationFailedMessage));
        }
    }

}
