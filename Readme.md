## Usage

在`config/app.php`中加入：

```php
 Wang\RabbitMQ\RabbitMQServiceProvider::class
```

执行命令：

```php
php artisan vendor:publish
```

### Publish

```php
$delayMessage = app(DelayedMessage::class);

$delayMessage->setExchange('delay-exchange');
$delayMessage->setQueue('delay-queue');

$delayMessage->publish([
    'name' => 'Tang',
    'age' => 22,
], 5000);

return 'success';
```


### Consume

```php

$delayMessage = app(DelayedMessage::class);

$delayMessage->setExchange('delay-exchange');
$delayMessage->setQueue('delay-queue');

$delayMessage->consume(function (AMQPMessage $message) {
    var_dump(json_decode($message->body, true));
});

```