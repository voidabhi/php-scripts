<?php
class WebWork extends Stackable {

    public function __construct() {
        $this->complete = false;
    }

    public function run() {
        $this->worker->logger->log(
            "%s executing in Thread #%lu", 
            __CLASS__, $this->worker->getThreadId());
        usleep(100);
        $this->complete = true;
    }

    public function isComplete() {
        return $this->complete;
    }

    protected $complete;
}

class WebWorker extends Worker {

    public function __construct(WebLogger $logger) {
        $this->logger = $logger;
    }

    protected $logger;
}

class WebLogger extends Stackable {

    protected function log($message, $args = []) {
        $args = func_get_args();    

        if (($message = array_shift($args))) {
            echo vsprintf(
                "{$message}\n", $args);
        }
    }
}

$logger = new WebLogger();
$pool = new Pool(8, WebWorker::class, [$logger]);

while (@$i++<10)
    $pool->submit(new WebWork());

usleep(2000000);

$logger->log("Shrink !!");

$pool->resize(1);
$pool->collect(function(WebWork $task){
    return $task->isComplete();
});

while (@$j++<10)
    $pool->submit(new WebWork());

$pool->shutdown();	
