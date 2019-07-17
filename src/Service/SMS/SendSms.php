<?php

namespace App\Service\SMS;

use AlibabaCloud\Client\AlibabaCloud;
use App\Repository\SmsLogRepository;

class SendSms
{
    const SIGN_NAME = '小海浪LaOla';

    const KEY_ID = 'LTAI7ubVjPqhX5VI';
    const KEY_SECRET = 'Cy55U1mRON6Pl8ZFIsBpCkO60WcU90';

    private $slr;

    public function __construct(SmsLogRepository $slr)
    {
        $this->slr = $slr;
    }

    public function sendSMS($name, $phone, $templateCode, $params)
    {
        // dd(func_get_args());
        AlibabaCloud::accessKeyClient(self::KEY_ID, self::KEY_SECRET)
            ->regionId('cn-shanghai')
            ->asGlobalClient();
        try {
            $res = AlibabaCloud::rpcRequest()
                ->product('Dysmsapi')
                ->version('2017-05-25')
                ->action('SendSms')
                ->options([
                    'query' => [
                        'PhoneNumbers' => $phone,
                        'SignName' => self::SIGN_NAME,
                        'TemplateCode' => $templateCode,
                        'TemplateParam' => json_encode($params),
                    ]
                ])
                ->request()
                ->toArray();
        } catch (\Exception $e) {
            throw $e;
        }

        if ('OK' === $res['Message']) {
            // 记录数据库
            $smsLog = $this->slr->create($name, $phone, $params);
            // return TRUE
            return $smsLog;
        }

        throw new \Exception('短信发送失败');
    }
}
