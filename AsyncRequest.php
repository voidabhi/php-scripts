<?php
/*
This script can be used by a server to make an asyncronous request to any given url.
This script assumes you have access to $_COOKIE and $_SERVER variable passed along via the server.
GET and POST requests are supported.
POST can have an attached file to it.
*/

// Make an asyncronous GET request
function async_get($ssl, $host, $port, $endpoint, $connectTimeout) {
	$cookie_str = '';
	
	foreach ($_COOKIE as $k => $v) {
		$cookie_str .= urlencode($k) .'='. urlencode($v) .'; ';
	}
	$sslstr = ($ssl) ? "ssl://" : "";
	$request =  "GET $endpoint HTTP/1.1\r\n";
	$request .= "Host: $host\r\n";
	if (!empty($cookie_str)) {
		$req .= 'Cookie: '. substr($cookie_str, 0, -2);
	}
	$request .= "Connection: Close\r\n";
	$request .= "\r\n";
	$errno = null;
	$errstr = null;
	$port = $port;
	if (($fp = @fsockopen($sslstr.$host, $port, $errno, $errstr, $connectTimeout)) == false) {
		return;
	}
	fputs($fp,$req);
	fclose($fp);
}
// Make an asyncronous POST request
function async_post($ssl, $host, $port, $endpoint, $connectTimeout, $file, $filetype) {
	$filename = basename($file);
	$cookie_str = '';
	foreach ($_COOKIE as $k => $v)
		$cookie_str .= urlencode($k) .'='. urlencode($v) .'; ';
	$sslstr = ($ssl) ? "ssl://" : "";
	$eol = "\r\n";
	$boundary = '137382108507214398510813094781';
	$filepreamble = '--' . $boundary . $eol;
	$filepreamble .= 'Content-Disposition: form-data; name="' . $filetype . '"; filename="' . $filename . '"' . $eol;
	$filepreamble .= 'Content-Type: application/octet-stream' . $eol . $eol;
	$fileepilogue = '--' . $boundary . '--';
	$clength = strlen($filepreamble) + filesize($file) + strlen($fileepilogue);
	$headers = "POST $endpoint HTTP/1.1" . $eol;
	$headers .= "Host: $host" . $eol;
	if (!empty($cookie_str))
		$headers .= 'Cookie: '. substr($cookie_str, 0, -2);
	$headers .= $eol;
	$headers .= 'Content-Type: multipart/form-data; boundary="' . $boundary . '"' . $eol;
	$headers .= 'Content-Length: ' . ($clength + 50) . $eol . $eol;
	$errno = null;
	$errstr = null;
	$port = $_SERVER['SERVER_PORT'];
	$connectTimeout = 2;
	$fdata = fopen($file,'r');
	if (($socket = @fsockopen($sslstr.$host, $port, $errno, $errstr, $connectTimeout)) == false) {
		return;
	}
	fputs($socket,$headers);
	fputs($socket,$filepreamble);
	//upload file content here
	while(!feof($fdata))
	{
		$rs = fread($fdata,1440);
		$nb = strlen($rs);
		fwrite($socket,$rs,$nb);
	}
	fwrite($socket, "\n", 1);
	fputs($socket, $fileepilogue);
	fclose($socket);
}
