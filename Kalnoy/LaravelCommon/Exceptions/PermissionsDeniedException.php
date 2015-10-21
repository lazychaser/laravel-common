<?php

namespace Kalnoy\LaravelCommon\Exceptions;

use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PermissionDeniedException extends HttpException
{
    /**
     * @param null $message
     * @param \Exception $previous
     * @param array $headers
     * @param null $code
     */
    public function __construct($message = null, \Exception $previous = null,
                                $headers = [ ], $code = null
    ) {
        parent::__construct(Response::HTTP_FORBIDDEN, $message, $previous, $headers, $code);
    }
}