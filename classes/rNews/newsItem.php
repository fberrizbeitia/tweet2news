<?php
include_once ("person.php");
include_once ("concept.php");
include_once ("place.php"); 
include_once ("organization.php");
@include_once ("EasyRdf.php");
@include_once ("../EasyRdf.php");
@include_once ("../EasyRdf/Graph.php");

class newsItem{
	var $subjectURI= null;
	
	var $headline = null;			// xsd:string
	var $provider = null;			// PERSON | ORGANIZATION
	var $creator = null;			// PERSON | ORGANIZATION
	var $dateCreated = null;		// xsd:dateTime
	var $datePublished = null;		// xsd:dateTime
	var $about = array();			// CONCEPT
	var $mentions= array();			// CONCEPT
	var $inLanguage = "en";			// xsd:string // languageng
	var $identifier = null; 		// xsd:string
	var $dateline = null; 			// xsd:string // Location of the news
	var $thumbnailUrl = null;
 
 
 	function __construct($headline = null,$provider = null, $dateCreated = null,$datePublished = null, $thumbnailUrl = null,$dateline= null,$identifier = null){
		$this->headline= $headline;
		$this->provider = $provider;
		$this->dateCreated = $dateCreated;
		$this->datePublished = $datePublished;
		$this->thumbnailUrl = $thumbnailUrl;
		$this->dateline = $dateline;
		$this->identifier = $identifier;
		
		$this->subjectURI = "<".$identifier.">";
	}
 
 
 
	function Addcreator($name = null,$description = null, $image = null, $url = null, $addInfoUri = null,$givenName = null, $additionalName = null, $familyName = null, $address = null, $honorificPrefix = null, $honorificSuffix = null){
		
		$this->creator = new person($url,$name,$description, $image, $url, $addInfoUri,$givenName, $additionalName, $familyName , $address , $honorificPrefix , $honorificSuffix );
	}
	
	
	function addPerson($target,$uri = null, $name = null,$description = null, $image = null, $url = null, $addInfoUri = null,$givenName = null, $additionalName = null, $familyName = null, $address = null, $honorificPrefix = null, $honorificSuffix = null){
		$person = new person($uri,$name,$description, $image, $url, $addInfoUri,$givenName, $additionalName, $familyName , $address , $honorificPrefix , $honorificSuffix );
		if($target == 'mentions'){
			array_push($this->mentions,$person);
		}else{
			if($target == 'about'){
				array_push($this->about,$person);	
			}
		}
		
	}
	
	function addOrganization($target,$uri = null,$name = null, $description = null, $image = null, $url = null,$addInfoUri = null,$tickerSymbol = null, $address = null){
		$org = new organization($uri,$name , $description , $image , $url ,$addInfoUri ,$tickerSymbol , $address );
		if($target == 'mentions'){
			array_push($this->mentions,$org);
		}else{
			if($target == 'about'){
				array_push($this->about,$org);	
			}
		}
	}
	
	function addPlace($target,$uri = null,$name = null,$description = null, $image = null, $url = null, $addInfoUri = null,$address = null, $geoCoordinates = null, $featureCode = null){
		$place = new place($uri,$name ,$description , $image , $url , $addInfoUri ,$address, $geoCoordinates , $featureCode);
		if($target == 'mentions'){
			array_push($this->mentions,$place);
		}else{
			if($target == 'about'){
				array_push($this->about,$place);	
			}
		}
	}
	
	function addConcept($target,$uri = null,$name = null, $description = null, $image = null, $url = null, $infoUri = null){
		$concept = new concept($uri,$name, $description, $image, $url, $infoUri);
		if($target == 'mentions'){
			array_push($this->mentions,$concept);
		}else{
			if($target == 'about'){
				array_push($this->about,$concept);	
			}
		}
	}
	
	function save(){
		
		$objEasyRDF = new EasyRdf_Graph();
		EasyRdf_Namespace::set("rNews","http://iptc.org/std/rNews/2011-10-07#");
		
		if($this->headline != null){
			$objEasyRDF->add($this->subjectURI,"rNews:headline",$this->headline);
		}
		
		if($this->provider != null){
			$objEasyRDF->add($this->subjectURI,"rNews:provider",$this->provider);
		}
		
		if($this->dateCreated != null){
			$objEasyRDF->add($this->subjectURI,"rNews:dateCreated",$this->dateCreated);
		}
		
		if($this->datePublished != null){
			$objEasyRDF->add($this->subjectURI,"rNews:datePublished",$this->datePublished);
		}
		
		if($this->inLanguage != null){
			$objEasyRDF->add($this->subjectURI,"rNews:inLanguage",$this->inLanguage);
		}
		
		if($this->identifier != null){
			$objEasyRDF->add($this->subjectURI,"rNews:identifier",$this->identifier);
		}
		
		if($this->thumbnailUrl != null){
			$objEasyRDF->add($this->subjectURI,"rNews:thumbnailUrl",$this->thumbnailUrl);
		}
		
		if($this->dateline != null){
			$objEasyRDF->add($this->subjectURI,"rNews:dateline",$this->dateline);
		} 
		
		//add the more complex elements
		
		if($this->creator != null){
			$Rcreator = $objEasyRDF->resource($this->creator->subjectURI,"rNews:person");			
			$this->creator->save($Rcreator);
			$objEasyRDF->addResource($this->subjectURI,"rNews:creator",$Rcreator);
			
		}
		
		for($i = 0; $i < count($this->about); $i++){
			$element = $this->about[$i];
			$type = $element->getType();
			if($type == 'person'){
				$RPerson = $objEasyRDF->resource($element->subjectURI,"rNews:person");			
				$element->save($RPerson);
				$objEasyRDF->addResource($this->subjectURI,"rNews:about",$RPerson);
			}
			
			if($type == 'concept'){
				$RConcept = $objEasyRDF->resource($element->subjectURI,"rNews:concept");			
				$element->save($RConcept);
				$objEasyRDF->addResource($this->subjectURI,"rNews:about",$RConcept);
			}
			
			if($type == 'organization'){
				$ROrg = $objEasyRDF->resource($element->subjectURI,"rNews:organization");			
				$element->save($ROrg);
				$objEasyRDF->addResource($this->subjectURI,"rNews:about",$ROrg);
			}	
			
			if($type == 'place'){
				$RPlace = $objEasyRDF->resource($element->subjectURI,"rNews:place");			
				$element->save($RPlace);
				$objEasyRDF->addResource($this->subjectURI,"rNews:about",$RPlace);
			}
			
		}
		/*
		var $about = array();			// CONCEPT
		var $mentions= array();			// CONCEPT
		*/
		
		
		var_dump($objEasyRDF->serialise('rdf'));
		
	}
	
	
 
}

?>