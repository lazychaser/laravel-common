<?php

namespace Kalnoy\LaravelCommon\Mail;

use Illuminate\Mail\Mailer;

/**
 * Notifier.
 */
class Notifier {

    /**
     * @var \Illuminate\Mail\Mailer
     */
    protected static $mailer;

    /**
     * Set a mailer.
     */
    public static function setMailer($mailer)
    {
        static::$mailer = $mailer;
    }

}