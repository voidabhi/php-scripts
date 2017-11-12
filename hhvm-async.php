<?hh
class Fetcher {
    // async function: http://docs.hhvm.com/manual/en/hack.async.php
    // return type annotation: http://docs.hhvm.com/manual/en/hack.annotations.introexample.php
    // generics: http://docs.hhvm.com/manual/en/hack.generics.php
    public async function fetch(string $url) : Awaitable<int> {        
        $ch1 = curl_init();
        print "get $url \n";
        curl_setopt($ch1, CURLOPT_URL, $url);
        curl_setopt($ch1, CURLOPT_HEADER, 0);
        curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
        
        $mh = curl_multi_init();
        curl_multi_add_handle($mh,$ch1);
        
        $active = null;
        do {
            $mrc = curl_multi_exec($mh, $active);
            // reschedule it for async to work properly
            await RescheduleWaitHandle::Create(1, 1);
        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        
        while ($active && $mrc == CURLM_OK) {
            if (curl_multi_select($mh) != -1) {
                do {
                    $mrc = curl_multi_exec($mh, $active);
                    await RescheduleWaitHandle::Create(1, 1); // simulate blocking I/O
                } while ($mrc == CURLM_CALL_MULTI_PERFORM);
            }
        }
        print "finished $url\n";
        return 1;
    }
}
class Fetch {
    
    protected Fetcher $fetcher;
    
    //Constructor Argument Promotion http://docs.hhvm.com/manual/en/hack.constructorargumentpromotion.php
    //Vector http://docs.hhvm.com/manual/en/hack.collections.vector.php (instead of array)
    //Annotating Arrays and Collections: http://docs.hhvm.com/manual/en/hack.annotations.arrays.php
    public function __construct(private Vector<string> $urls) {
        $this->fetcher = new Fetcher();
    }
    
    public async function run() : Awaitable<int> {
        
        //lambda expressions: http://docs.hhvm.com/manual/en/hack.lambda.php
        $waithandles = $this->urls->map($url ==> $this->fetcher->fetch($url));
        
        // works too, but not supported by typechecker yet
        //$waithandles = $this->urls->map((string $url): Awaitable<int> ==> $this->fetcher->fetch($url));
        // same as above, but with closure and annotations, so it catches type errors
        //$waithandles = $this->urls->map(function(string $url): Awaitable<int> {return $this->fetcher->fetch($url);});
        
        //create wait handle for all and only continue when all have finished
        $x = await GenVectorWaitHandle::Create($waithandles);
        return $x->count();
    }
    
    public function start() : string {
        return $this->run()->join() . " urls fetched and finished\n";
    }
}
function main() {
    //Vector with Literal Syntax: http://docs.hhvm.com/manual/en/hack.collections.literalsyntax.php
    $urls = Vector{"http://chregu.tv/webinc/sleep.php?s=1","http://chregu.tv/webinc/sleep.php?s=1"};
    $f = new Fetch($urls);
    print $f->start();
}
main();
