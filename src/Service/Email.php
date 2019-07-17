<?php

namespace App\Service;

class Email
{
    private $mailer;
    public function __construct()
    {
        $transport = (new \Swift_SmtpTransport('smtp.exmail.qq.com', 587, 'tls'))
            ->setUsername('info@xiaohailang.net')
            ->setPassword('Sz123!@#');
        $this->mailer = new \Swift_Mailer($transport);
    }
    public function send(\Swift_Message $msg)
    {
        $msg->setFrom(['info@xiaohailang.net' => '小海浪']);
        
        return $this->mailer->send($msg);
    }
}
