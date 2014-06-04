<?php

namespace Kalnoy\LaravelCommon\Mail;

use Illuminate\Mail\Mailer;

/**
 * Admin notifier.
 */
class AdminNotifier extends Notifier {

    /**
     * A list of admin emails.
     * 
     * @var array
     */
    protected $emails;

    /**
     * Init notifier.
     * 
     * @param array $emails
     */
    public function __construct(array $emails)
    {
        $this->emails = $emails;
    }

    /**
     * Send a mail to the admins.
     *
     * @param string $view
     * @param array $data
     * @param string $subject
     *
     * @return int
     */
    protected function notify($view, $data, $subject = '')
    {
        return static::$mailer->send($view, $data, function ($message) use ($subject)
        {
            $emails = $this->emails;

            // First person to receieve a mail
            $message->to(array_shift($emails));

            // Others will receive a copy
            foreach ($emails as $email)
            {
                $message->cc($email);
            }

            if ($subject) $message->subject($subject);
        });
    }

}