<?php

namespace Wang\RabbitMQ;

use Illuminate\Support\ServiceProvider;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/rabbitmq.php' => config_path('rabbitmq.php'),
        ]);
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton(AMQPStreamConnection::class, function () {
            return new AMQPStreamConnection(
                config('rabbitmq.host'),
                config('rabbitmq.port'),
                config('rabbitmq.user'),
                config('rabbitmq.password'),
                config('rabbitmq.vhost'));
        });

        $this->app->singleton(DelayedMessage::class, function ($app) {
            return new DelayedMessage($app[AMQPStreamConnection::class]);
        });
    }
}
