<?php

class DAG
{

    private static $directory = '';

    function __construct()
    {

        self::$directory =  __DIR__."/files/*.txt";
        $this->read();

    }

    public function read(){

        $edges = array();

        foreach (glob(self::$directory) as $filename) {

            if( $handle = @fopen( $filename, "r") ){

                while ($line = @fgets($handle)) {

                    $path = explode(' ', $line);

                    if(count($path)===3){

                        if (!isset($edges[$path[0]])) {
                            $edges[$path[0]] = [];
                        }

                        $edges[$path[0]][$path[1]] = $path[2];

                    }

                }
                fclose($handle);

            }

        }
        $this->points = $edges;

        foreach ($this->points as $key => $value) {
            $this->paths[] = [$key];
        }

        return $edges;
    }

    public $points = [];
    public $paths = [];

    public function run(){

        $notend_point = array_keys($this->points);
        $paths = [];
        foreach ($this->paths as $value) {

            $end_point = end($value);


            if ($end_point === 0){
                $paths[] = $value;
                continue;
            }

            if (!in_array($end_point, $notend_point)) {
                $value[] = 0;
                $paths[] = $value;
                continue;
            }

            $last_path = $this->getPath($end_point);

            foreach ($last_path as $new) {
                $new_value = $value;
                $new_value[] = $new;
                $paths[] = $new_value;
            }
        }

        $this->paths = $paths;

    }

    public function checkEndAllPaths(){
        $last_path = [];
        foreach ($this->paths as $value) {
            $last_path[] = end($value);
        }
        return array_sum($last_path) === 0;
    }

    public function getPath($key){

        if (!isset($this->points[$key])) {
            return [0];
        }

        $paths = [];

        foreach ($this->points[$key] as $key => $value) {
            $paths[] = $key;
        }

        return $paths;

    }

    public function getLength($path){

        $count = 0;
        foreach ($path as $key => $value) {
            $start = $value;
            $end = $path[$key+1];
            $length = $this->points[$start][$end];
            $count += intval($length);
        }

        return $count;

    }
}

$find = new DAG();

while ($find->checkEndAllPaths() === false){
    $find->run();
}

$array = [];
foreach ($find->paths as $path) {
    $length = $find->getLength($path);
    $array[join('-', $path)] = $length;
}
arsort($array);
print_r($array);
