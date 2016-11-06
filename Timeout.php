<?php
class TimeoutException extends RuntimeException {}
class Timeout
{
    private $active;
    public function set($seconds)
    {
        $this->active = true;
        declare(ticks = 1);
        pcntl_signal(SIGALRM, array($this, 'handle'), true);
        pcntl_alarm($seconds);
    }
    public function clear()
    {
        $this->active = false;
    }
    public function handle($signal)
    {
        echo "received signal\n";
        if ($this->active) {
            throw new TimeoutException();
        }
    }
}

$timeout = new Timeout();
$start   = microtime(true);
try {
    echo "setting timeout to 1 second\n";
    $timeout->set(1); // set a 1 second timeout
    echo "sleeping for 10 seconds\n";
    sleep(10); // some long running operation...
    echo "clearing 1 second timeout\n";
    $timeout->clear(); // clear timeout
} catch(TimeoutException $e) {
    // timed out
    echo "caught a TimeoutException\n";
}
$total = microtime(true) - $start;
echo "time spent {$total}\n";
