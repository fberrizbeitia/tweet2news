<?php
include_once("concept.php");
@include_once ("../EasyRdf/Graph.php");

class person extends concept{

	var $givenName;			// xsd:string Given name. In the U.S., the first name of an individual. Also used if person has only one name
	var $additionalName;	// xsd:string
	var $familyName; 		// xsd:string
	var $address; 			// PostalAddress
	var $honorificPrefix;	// xsd:string
	var $honorificSuffix; 	// xsd:string
	
	function __construct($uri = null,$name = null,$description = null, $image = null, $url = null, $addInfoUri = null, $givenName = null, $additionalName = null, $familyName = null, $address = null, $honorificPrefix = null, $honorificSuffix = null){
		parent::__construct($uri,$name,$description,$image,$url,$addInfoUri);

		$this->givenName = $givenName;
		$this->additionalName = $additionalName;
		$this->familyName = $familyName;
		$this->address = $address;
		$this->honorificPrefix = $honorificPrefix;
		$this->honorificSuffix = $honorificSuffix;
	}
	
	function getType(){
		return 'person';
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
		
		if($this->givenName != null){
			$EasyRdfResource->add("rNews:givenName",$this->givenName);
		}
		
		if($this->additionalName != null){
			$EasyRdfResource->add("rNews:additionalName",$this->additionalName);
		}
		
		if($this->familyName != null){
			$EasyRdfResource->add("rNews:familyName",$this->familyName);
		}
		
		if($this->address != null){
			$EasyRdfResource->add("rNews:address",$this->address);
		}
		
		if($this->honorificPrefix != null){
			$EasyRdfResource->add("rNews:honorificPrefix",$this->honorificPrefix);
		}
		
		if($this->honorificSuffix != null){
			$EasyRdfResource->add("rNews:honorificSuffix",$this->honorificSuffix);
		}
	}

}

?>