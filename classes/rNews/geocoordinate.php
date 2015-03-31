<?php

class geoCoordinate{
	
	var $point;		// xsd:string
	var $line;		// xsd:string
	var $polygon;	// xsd:string
	var $box;		// xsd:string
	var $circle;	// xsd:string
	var $elevation;	// xsd:string
	
	function __construct($point = null, $line = null, $polygon = null, $box = null, $circle = null, $elevation = null){
		$this->point= $point;
		$this->line = $line;
		$this->polygon = $polygon;
		$this->box = $box;
		$this->circle = $circle;
		$this->elevation = $elevation;
	}
}

?>