<?php

# Kafka configuration (https://github.com/edenhill/librdkafka/blob/master/CONFIGURATION.md)
$config = { "metadata.broker.list" => "broker1.example.com", "socket.timeout.ms" => 30000, ... };

# Create Kafka Producer object.
# Brokers are specified through $config, but we could make it easier for people by having the first
# argument be brokers as well, and rdkafka will use all brokers specified?
$producer = new Kafka::Producer([$brokers,]? $config);

# Topic configuration (optional)
$topic_config = { "request.required.acks" => 3 };

# Create topic from Kafka object.
# Either:
$topic1 = new Kafka::Topic($producer, "mytopic", $topic_config);
# Or:
$topic1 = $producer->new_topic("mytopic", $topic_config);
# Or some other way. In any case it must be passed the $producer or $consumer in some way.
# I dont know whats more PHP idiomatic.


# Produce message.
#  $key is optional, how?
#  $partition is either a number for a specific partition, or -1 for using the partitioner (defaults to random),
#  maybe a const name for -1?

# Either:
$producer->produce($topic1, $partition, $key, $msg);
# Or;
$topic1->produce($partition, $key, $msg);
# Or some other way? produce only needs the topic object, not the $producer. But I dont know whats more clean.

# The produce request may also signal back to the application when the message has actually been delivered.
# This is done by registering a callback and calling rd_kafka_poll(), we could do something similar for PHP but maybe in the
# next iteration.



# Consumer, same way as producer.
$config = { "metadata.broker.list" => "broker1.example.com", "socket.timeout.ms" => 30000, ... };

# Create Kafka consumer
$consumer = new Kafka::Consumer($config);

# Topic configuration (optional)
$topic_config = { "offset.store.method" => "broker" };

# Create topic from Kafka object.
$topic1 = new Kafka::Topic($consumer, "mytopic", $topic_config);

# Start consumer
# Offset is either a numeric offset or: beginning, end, stored
$consumer->start($partition, $offset);

# Consume messages, one at a time.
foreach ($consumer->consume() as $msginfo) {
   # Each message returned contains an error (if something went wrong), the message's offset, payload and key.
   ($err,$offset,$msg,$key) = $msginfo;

   if ($err) {
      print "ouch: $err\n";
   } else {
      print "received message at offset $offset with key $key: $msg\n";
      # Force store/commit of offset (typically not needed)
      $topic1->commit($partition, $offset);
   }
}

# Stop consumer
$consumer->stop($partition);
