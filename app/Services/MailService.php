<?php

namespace App\Services;

use App\Mail\MailRegistrationInfo;
use Mail;

class MailService
{
    public function mailRegistrationInfo(array $recipients, array $registrationInfo)
    {
        $results = [];
        try {
            foreach ($recipients as $key => $recipient) {
                $data = [
                    'subject' => 'Registration Info',
                ];

                $data = array_merge($data, $registrationInfo);

                $result = Mail::to($recipient, $recipient)->send(new MailRegistrationInfo($data));
                $results[] = $result;
            }
        } catch (\Throwable $error) {
            $results[] = $error->getMessage();
            error_log(__METHOD__ . ' - Line ' . $error->getLine() . ': ' . $error->getMessage());
        }

        return $results;
    }
}