<?php
use Resque;
use ResqueScheduler\ResqueScheduler;
/**
 * Base class for recurring Resque jobs.
 *
 * Jobs must implement the recurrentSetUp(), recurrenctPerform(), and recurrentTearDown()
 * methods instead of the normal setUp(), perform(), and tearDown() methods.
 *
 * Note that each recurring job is restricted to being scheduled only once, meaning
 * that any class extending this class cannot run at e.g. both every 3 minutes and every 5 minutes.
 * If a job is scheduled, and another schedule already exists for the same class, the old schedule
 * will be overwritten.
 */
class RecurringJob {
    const KEY_RECURRING_JOBS = "recurring";
    /**
     * @param int $interval How often should this task be run (seconds)
     * @param string $queue Queue name
     * @param string $class Name of job class to run
     * @param array $args Arguments to job
     * @param bool $trackStatus
     * @param bool $runImmediately True if the job should be run immediately,
     * false to wait $interval seconds before first run
     */
    protected static function schedule( $interval, $queue, $class, $args=[], $trackStatus=false, $runImmediately=true ) {
        $args = [
            'recur' => [
                'interval' => $interval,
                'queue' => $queue,
                'class' => $class,
                'args' => $args,
                'trackStatus' => $trackStatus,
            ]
        ];
        if ( $runImmediately ) {
            $id = Resque::enqueue( $queue, $class, $args, $trackStatus );
        } else {
            $id = ResqueScheduler::enqueueIn( $interval, $queue, $class, $args, $trackStatus );
        }
        /*
         * Mark this scheduled job as the 'right' one to run, in effect causing
         * all other scheduled jobs of the same class to not run.
         *
         * shouldPerform() checks if the job has the right ID, as set below.
         */
        $key = self::KEY_RECURRING_JOBS . ":" . static::getJobIdentifier( $class );
        Resque::redis()->set( $key, $id );
    }
    public function recurringSetUp() {}
    public function recurringPerform() {}
    public function recurringTearDown() {}
    public final function setUp() {
        if ( !$this->shouldPerform() ) {
            throw new \Resque_Job_DontPerform();
        }
        $this->recurringSetUp();
    }
    public final function perform() {
        /*
         * Catching exceptions here, we won't re-throw them, as that will cause the tearDown() method to not be called.
         * We need the tearDown() method to be called in order to schedule the recurring job again.
         */
        try {
            $this->recurringPerform();
        } catch (\Exception $e) {
            $this->logException( $e );
        }
    }
    public final function tearDown() {
        $this->recurringTearDown();
        /*
         * Remove the schedule key.
         * scheduleAgain() will set it again.
         */
        $class_hash = md5( $this->args['recur']['class'] );
        $class_key = self::KEY_RECURRING_JOBS . ":" . $class_hash;
        Resque::redis()->delete( $class_key );
        $this->scheduleAgain();
    }
    /**
     * Check if this job is valid to run.
     *
     * If another job of the same class has been scheduled after this one was scheduled,
     * this job won't run, but instead the other will.
     *
     * @return bool
     */
    private function shouldPerform() {
        $key = self::KEY_RECURRING_JOBS . ":" . static::getJobIdentifier( get_called_class() );
        return Resque::redis()->get( $key ) == $this->job->payload['id'];
    }
    /**
     * Returns the identifier for the current job.
     *
     * At this time, the identifier is a hash of the extending class name.
     * It could be changed to allow multiple instances of the same job to happen.
     *
     * @return string
     */
    private static function getJobIdentifier( $class ) {
        return md5( $class );
    }
    /**
     * Schedule the job to run again.
     */
    private function scheduleAgain()
    {
        $recurArgs = $this->args['recur'];
        $interval = $recurArgs['interval'];
        $queue = $recurArgs['queue'];
        $class = $recurArgs['class'];
        $args = $recurArgs['args'];
        $trackStatus = $recurArgs['trackStatus'];
        self::schedule($interval, $queue, $class, $args, $trackStatus, false);
    }
}
