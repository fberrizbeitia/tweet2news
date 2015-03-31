<?php

include_once("concept.php");

class place extends concept{
	var $subjectURI;
	
	var $address;			// PostalAddress
	var $geoCoordinates;	// GeoCoordinates
	var $featureCode; 		// xsd:string

	function __construct($uri = null,$name = null,$description = null, $image = null, $url = null, $addInfoUri = null,$address = null, $geoCoordinates = null, $featureCode = null){
		parent::__construct($uri,$name,$description,$image,$url,$addInfoUri);
		$this->address = $address;
		$this->geoCoordinates = $geoCoordinates;
		$this->featureCode = $featureCode;
	}
	
	function getType(){
		return 'place';
	}
	
	function save(EasyRdf_Resource $EasyRdfResource){
		
		if($this->name != null){
			$EasyRdfResource->add("rNews:name",$this->name);
		}
		
		if($this->description != null){
			$EasyRdfResource->add("rNews:description",$this->description);
		}
		
		if($this->url != null){
			$EasyRdfResource->add("rNews:url",$this->url);
		}
		
		if($this->image != null){
			$EasyRdfResource->add("rNews:image",$this->image);
		}
		
		if($this->featureCode != null){
			$EasyRdfResource->add("rNews:featureCode",$this->featureCode);
		}
			
	}
}

?>