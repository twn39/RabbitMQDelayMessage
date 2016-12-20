<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Wang\RabbitMQ\DelayedMessage;

class QueueController extends Controller
{

    public function index()
    {
        $delayMessage = app(DelayedMessage::class);

        $delayMessage->setExchange('delay-exchange');
        $delayMessage->setQueue('delay-queue');

        $delayMessage->publish([
            'name' => 'Tang',
            'age' => 22,
        ], 5000);

        return 'success';
    }
}
