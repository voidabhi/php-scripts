<?php
class WebSocket
{
	const MAGIC = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';
	
	public function listen($addr, $function)
	{
		$server = stream_socket_server("tcp://$addr", $errno, $error);
		if (!$server) {
			throw new \Exception("Unable to create server: $error");
		}
		$client = @stream_socket_accept($server, 3);
		if ($client) {
			$this->connect($client, $function);
		}
		fclose($server);
	}
	public function connect($client, $function)
	{
		$headers = stream_get_line($client, 65535, "\r\n\r\n");
		if (!preg_match('#^Sec-WebSocket-Key: (\S+)#mi', $headers, $match)) {
			return;
		}
		fwrite($client, "HTTP/1.1 101 Switching Protocols\r\n"
			. "Upgrade: websocket\r\n"
			. "Connection: Upgrade\r\n"
			. "Sec-WebSocket-Accept: " . base64_encode(sha1($match[1] . self::MAGIC, TRUE))
			. "\r\n\r\n");
		for(;;) {
			$s = fread($client, 65535);
			if (!$s) {
				break;
			}
			$res = $function($this->decode($s));
			fwrite($client, $this->encode($res));
		}
		fclose($client);
	}
	private function decode($frame)
	{
		$len = ord($frame[1]) & 127;
		if ($len === 126) {
			$ofs = 8;
		} elseif ($len === 127) {
			$ofs = 14;
		} else {
			$ofs = 6;
		}
		$text = '';
		for ($i = $ofs; $i < strlen($frame); $i++) {
			$text .= $frame[$i] ^ $frame[$ofs - 4 + ($i - $ofs) % 4];
		}
		return $text;
	}
	private function encode($text)
	{
		$b = 129; // FIN + text frame
		$len = strlen($text);
		if ($len < 126) {
			return pack('CC', $b, $len) . $text;
		} elseif ($len < 65536) {
			return pack('CCn', $b, 126, $len) . $text;
		} else {
			return pack('CCNN', $b, 127, 0, $len) . $text;
		}
	}
}
