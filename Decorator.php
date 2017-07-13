<?php
class CD {
	public $trackList;
	
	public function __construct(){
		$this->trackList = array();
	}
	public function addTrack($track){
		$this->trackList[] = $track;
	}
	public function getTrackList(){
		$output = '';
		
		foreach ($this->trackList as $num=>$track){
			$output .= ($num + 1) . ") {$track}. ";
		}
		
		return $output;
	}
}
class CDTrackListDecoratorCaps{
	private $__cd;
	
	public function __construct(CD $cd){
		$this->__cd = $cd;
	}
	
	public function makeCaps(){
		foreach ($this->__cd->trackList as &$track){
			$track = strtoupper($track);
		}
	}
}
$tracksFromExternalSource = array('What It Means', 'Brr', 'Goodbye');
$myCD = new CD();
foreach ($tracksFromExternalSource as $track) {
	$myCD->addTrack($track);
}
$myCDCaps = new CDTrackListDecoratorCaps($myCD);
$myCDCaps->makeCaps();
print "The CD contains the following tracks: " . $myCD->getTrackList();
?>
