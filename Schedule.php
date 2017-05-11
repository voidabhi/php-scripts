<?php
use Crunz\Schedule;

$schedule = new Schedule();

$x = 12;
$schedule->run(function() use ($x) { 
   // Do some cool stuff in here 
})       
 ->before(function() { 
     // Do something before the task runs
 })
 ->before(function() { 
         // Do something else
 })
 ->after(function() {
     // After the task is run
 });
->everyMinute()
->description('Copying the project directory');
         
return $schedule;
