<?php
@include_once ("../EasyRdf/Graph.php");

class concept{
	var $subjectURI;
	
	var $name = null;
	
	// from http://schema.org/Thing
	var $description = null;
	var $image = null;
	var $url = null; 
	
	// included by us
	var $aditionalInfoUri = null;

	function __construct($uri = null,$name = null, $description = null, $image = null, $url = null, $infoUri = null){
		$this->subjectURI = $uri;
		$this->name = $name;
		$this->description = $description;
		$this->image = $image;
		$this->url = $url;
		
		$this->aditionalInfoUri = $infoUri;
	}
	
	function getType(){
		return 'concept';
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
		
		if($this->aditionalInfoUri != null){
			$EasyRdfResource->add("rNews:aditionalInfoUri",$this->aditionalInfoUri);
		}
		
	}
	
}

?>