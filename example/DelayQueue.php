<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Message\AMQPMessage;
use Wang\RabbitMQ\DelayedMessage;

class DelayQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delay:queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'consume the delay task.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $delayMessage = app(DelayedMessage::class);

        $delayMessage->setExchange('delay-exchange');
        $delayMessage->setQueue('delay-queue');

        $delayMessage->consume(function (AMQPMessage $message) {
            var_dump(json_decode($message->body, true));
        });
    }
}
