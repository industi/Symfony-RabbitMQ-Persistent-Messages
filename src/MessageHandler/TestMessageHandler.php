<?php

namespace App\MessageHandler;

use App\Message\TestMessage;

class TestMessageHandler
{

    public function __invoke(TestMessage $message)
    {
        echo $message->getData() . PHP_EOL;
    }
}