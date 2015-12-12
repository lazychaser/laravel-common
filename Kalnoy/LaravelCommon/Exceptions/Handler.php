<?php

namespace Kalnoy\LaravelCommon\Exceptions;

use Exception;
use Illuminate\Contracts\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    protected $validationFailedMessage;

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception $e
     *
     * @return void
     */
    public function report(Exception $e)
    {
        if ($this->isHttpException($e) && $e->getStatusCode() == 404 ||
            $e instanceof ModelNotFoundException
        ) {
            /** @var \Illuminate\Http\Request $request */
            $request = app('request');

            $this->log->error('Not found: '.$request->fullUrl());
        } else {
            parent::report($e);
        }
    }

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
            $response = back()->withInput()->withErrors($e->errors());

            if ($this->validationFailedMessage) {
                $response->with('warning', trans($this->validationFailedMessage));
            }

            return $this->toIlluminateResponse($response, $e);
        } elseif ($e instanceof ModelNotFoundException) {
            $e = new NotFoundHttpException($e->getMessage(), $e);
        }

        if ($this->isHttpException($e)) {
            if ($this->isNonTextRequest($request)) {
                return $this->toIlluminateResponse(
                    response('', $e->getStatusCode()),
                    $e
                );
            }

            return $this->renderHttpException($e);
        } else {
            return parent::render($request, $e);
        }
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

}
