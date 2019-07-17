<?php

namespace App\Service\SMS;

class ChangePassword extends SendSms implements SendSmsInterface
{
    const CN_CODE = 'SMS_158645108';
    const OTHER_CODE = 'SMS_163053235';

    public function getTemplateCode(string $region = '86')
    {
        return '86' == $region ? self::CN_CODE : self::OTHER_CODE;
    }

    public function send($phone, $params, $region = '86')
    {
        $res = $this->sendSMS(self::class, $phone, $this->getTemplateCode($region), $params);
        return $res;
    }
}
