<?php

include_once("concept.php");

class place extends concept{
	var $subjectURI;
	
	var $address;			// PostalAddress
	var $geoCoordinates;	// GeoCoordinates
	var $featureCode; 		// xsd:string

}

?>