<?php

namespace Kalnoy\LaravelCommon\Mail;

use Mailer;
use Closure;

/**
 * Notifier.
 */
class Notifier {

    /**
     * Send an email.
     * 
     * @param string $view
     * @param array $data
     * @param \Closure $callback
     */
    public function send($view, $data, Closure $callback)
    {
        Mail::queue($view, $data, $callback);
    }

}