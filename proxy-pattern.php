
<?php
/**
 *
 * Proxy Pattern
 *
 * A proxy pattern creates an entry point which interacts behind the scenes with other objects.
 *  Can be useful for implementing access control, to implement lazy loading of resource intensive
 *  objects, or to simply act as a wrapper to reduce the options available to another more complex object
 */
interface HttpInterface
{
    public function get();
}
/**
 * HttpProxy Usage Instructions:
 *
 * $proxy = new HttpProxy('http://rss.cnn.com/rss/cnn_world.rss');
 * echo $proxy->get();
 *
 */
class HttpProxy implements HttpInterface
{
    protected $address;
    protected $string;
    /**
     * Constructor
     *
     * @param  $address
     */
    public function  __construct($address)
    {
        $this->address = filter_var($address, FILTER_SANITIZE_URL);
    }
    /**
     * Method uses HTTP address to retrieve contents of the page
     *
     * @return  mixed
     * @throws  Exception
     */
    public function get()
    {
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $this->address);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($handle, CURLOPT_HEADER, 0);
        try {
            $data = curl_exec($handle);
            $response = curl_getinfo($handle);
            if ($response['http_code'] == 200) {
            } else {
                throw new Exception;
            }
            curl_close($handle);
        } catch (Exception $e) {
            throw new Exception ('Request for address: ' . $this->address . ' failed.');
        }
        $this->string = $data;
        return $this->__toString();
    }
    /**
     * Format output as a string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->string;
    }
}
/**
 *  Try the Proxy Pattern Example:
 *
 *  1. Download and place file on your local server.
 *  2. Open the file using your browser.
 */
$proxy = new HttpProxy('http://www.youtube.com/watch?v=oHg5SJYRHA0');
echo $proxy->get();
