<?php

namespace Wang\RabbitMQ;

use Closure;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use Wang\RabbitMQ\Contracts\DelayMessageInterface;
use Wang\RabbitMQ\Exceptions\MQException;

class DelayedMessage implements DelayMessageInterface
{
    private $connection;
    private $channel;
    private $exchange;
    private $queue;

    private $bound = false;

    public function __construct(AMQPStreamConnection $connection)
    {
        $this->connection = $connection;
        $this->channel = $this->connection->channel();
    }

    public function bind()
    {
        if (empty($this->exchange)) {
            throw new MQException('RabbitMQ delay exchange is required !');
        }

        if (empty($this->queue)) {
            throw new MQException('RabbitMQ delay queue is required !');
        }

        $this->channel->exchange_declare($this->exchange, 'x-delayed-message', false, true, false, false, false, new AMQPTable(array(
            'x-delayed-type' => 'fanout',
        )));
        $this->channel->queue_declare($this->queue, false, false, false, false, false, new AMQPTable(array(
            'x-dead-letter-exchange' => 'delayed',
        )));

        $this->channel->queue_bind($this->queue, $this->exchange);
    }

    /**
     * @param $exchange
     */
    public function setExchange($exchange)
    {
        $this->exchange = $exchange;
    }

    /**
     * @param $queue
     */
    public function setQueue($queue)
    {
        $this->queue = $queue;
    }

    /**
     * 发布消息.
     *
     * @param $message
     * @param $ttl
     */
    public function publish($message, $ttl)
    {
        if (!$this->bound) {
            $this->bind();
            $this->bound = true;
        }
        $headers = new AMQPTable(array('x-delay' => $ttl));
        $message = new AMQPMessage(json_encode($message), array('delivery_mode' => 2));
        $message->set('application_headers', $headers);
        $this->channel->basic_publish($message, $this->exchange);
    }

    /**
     * 消费消息.
     *
     * @param $callback
     */
    public function consume(Closure $callback)
    {
        $this->channel->basic_consume($this->queue, '', false, true, false, false, $callback);

        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }
    }

    public function __destruct()
    {
        $this->channel->close();
        $this->connection->close();
    }
}
