// include the AMQPlib Classes || use an autoloader
    
    // queue/exchange names
    $queueRightNow = 'right.now.queue';
    $exchangeRightNow = 'right.now.exchange';
    $queueDelayed5sec = 'delayed.five.seconds.queue';
    $exchangeDelayed5sec = 'delayed.five.seconds.exchange';
    
    $delay = 5; // delay in seconds
    
    // create connection
    $AMQPConnection = new \PhpAmqpLib\Connection\AMQPConnection('localhost',5672,'guest','guest');
    
    // create a channel
    $channel = $AMQPConnection->channel();
    
    // create the right.now.queue, the exchange for that queue and bind them together
    $channel->queue_declare($queueRightNow);
    $channel->exchange_declare($exchangeRightNow, 'direct');
    $channel->queue_bind($queueRightNow, $exchangeRightNow);
    
    // now create the delayed queue and the exchange
    $channel->queue_declare(
            $queueDelayed5sec,
            false,
            false,
            false,
            true,
            true,
            array(
                'x-message-ttl' => array('I', $delay*1000),   // delay in seconds to milliseconds
                "x-expires" => array("I", $delay*1000+1000),
                'x-dead-letter-exchange' => array('S', $exchangeRightNow) // after message expiration in delay queue, move message to the right.now.queue
            )
    );
    $channel->exchange_declare($exchangeDelayed5sec, 'direct');
    $channel->queue_bind($queueDelayed5sec, $exchangeDelayed5sec);
    
    // now create a message und publish it to the delayed exchange
    $msg = new \PhpAmqpLib\Message\AMQPMessage(
        time(),
        array(
            'delivery_mode' => 2
        )
    );
    $channel->basic_publish($msg,$exchangeDelayed5sec);
    
    
    // consume the delayed message
    $consumeCallback = function(\PhpAmqpLib\Message\AMQPMessage $msg) {
        $messagePublishedAt = $msg->body;
        echo 'seconds between publishing and consuming: '
            . (time()-$messagePublishedAt) . PHP_EOL;
    };
    $channel->basic_consume($queueRightNow, '', false, true, false, false, $consumeCallback);
    
    // start consuming
    while (count($channel->callbacks) > 0) {
        $channel->wait();
    }
