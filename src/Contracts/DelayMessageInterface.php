<?php

namespace Wang\RabbitMQ\Contracts;

use Closure;

interface DelayMessageInterface
{
    public function publish($message, $ttl);

    public function consume(Closure $callback);
}
