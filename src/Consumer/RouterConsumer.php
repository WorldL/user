<?php

namespace App\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;

class RouterConsumer implements ConsumerInterface
{
    public function execute(\PhpAmqpLib\Message\AMQPMessage $msg)
    {
    }
}
