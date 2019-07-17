<?php

namespace App\Service\SMS;

interface SendSmsInterface
{
    /**
     * 获取阿里云template code
     *
     * @return string
     */
    public function getTemplateCode(string $region = '086');

    public function send($phone, $params);
}
