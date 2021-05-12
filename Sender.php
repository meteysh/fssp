<?php

/**
 *
 */
class Sender
{
    protected $mailer;

    public function __construct()
    {
        $transport = (new Swift_SmtpTransport(HOST, PORT, ENCRYPTION))
            ->setUsername(USERNAME)
            ->setPassword(PASSWORD);

        $this->mailer = new Swift_Mailer($transport);
    }

    public function send($subject, $msg)
    {

        $message = (new Swift_Message($subject))
            ->setFrom([USERNAME => NAME])
            ->setTo([TO])
            ->setBody($msg);

        return $this->mailer->send($message);
    }
}
